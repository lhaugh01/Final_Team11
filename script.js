document.addEventListener('DOMContentLoaded', () => {
  const API_KEY = '4fd186b10fe65f080443247342f9cc5c';
  const BASE_URL = 'https://api.themoviedb.org/3';

  const searchInput = document.getElementById('searchInput');
  const sortOptions = document.getElementById('sortOptions');
  const searchBtn = document.getElementById('searchBtn');
  const moviesDiv = document.getElementById('movies');

  async function fetchMovies(query = 'avengers', sort = 'popularity.desc') {
    const url = `${BASE_URL}/search/movie?api_key=${API_KEY}&query=${encodeURIComponent(query)}&sort_by=${sort}`;
    try {
      const res = await fetch(url);
      const data = await res.json();
      moviesDiv.innerHTML = data.results.length
        ? data.results.map(movieCardHTML).join('')
        : `<p class="error">No movies found for "${query}".</p>`;

      document.querySelectorAll('.movie-card').forEach((card, index) => {
        card.addEventListener('click', () => {
          const movie = data.results[index];
          const movieData = {
            title: movie.title,
            id: movie.id,
            posterPath: movie.poster_path,
            overview: movie.overview
          };
          openMovieModal(movieData);
          updateSearchHistory(movieData.title, movieData.id);
        });
      });

    } catch (err) {
      console.error('Failed to fetch movies:', err);
      moviesDiv.innerHTML = `<p class="error">Error loading movies. Please try again later.</p>`;
    }
  }

  function movieCardHTML(movie) {
    const posterPath = movie.poster_path
      ? `https://image.tmdb.org/t/p/w300${movie.poster_path}`
      : 'https://via.placeholder.com/200x300?text=No+Image';
    return `
      <div class="movie-card">
        <img src="${posterPath}" alt="${movie.title}" class="poster">
        <div class="movie-info">
          <h3>${movie.title}</h3>
          <p><strong>Release:</strong> ${movie.release_date || 'N/A'}</p>
          <p><strong>Popularity:</strong> ${movie.popularity}</p>
          <p>${movie.overview || 'No description available.'}</p>
        </div>
      </div>
    `;
  }

  function handleMovieSearch() {
    const query = searchInput.value.trim() || 'avengers';
    const sort = sortOptions.value;
    fetchMovies(query, sort);
  }

  if (searchInput) searchInput.addEventListener('input', handleMovieSearch);
  if (sortOptions) sortOptions.addEventListener('change', handleMovieSearch);
  if (searchBtn) searchBtn.addEventListener('click', handleMovieSearch);

  fetchMovies();

  async function updateSearchHistory(movieTitle, movieId) {
    try {
      const userId = localStorage.getItem('userId');
      if (!userId) return;

      await fetch('update_history.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId, movieTitle, movieId })
      });
    } catch (err) {
      console.error('Failed to update search history:', err);
    }
  }

  function openMovieModal(movie) {
    const movieModal = document.getElementById('movieModal');
    if (!movieModal) return;

    const posterUrl = movie.posterPath
      ? `https://image.tmdb.org/t/p/w500${movie.posterPath}`
      : 'https://via.placeholder.com/200x300?text=No+Image';

    movieModal.innerHTML = `
      <div class="modal-content movie-modal">
        <span class="close-button" id="closeMovieModal">&times;</span>

        <div class="fake-player">
          <div class="play-icon">&#9658;</div>
        </div>

        <div class="movie-details">
          <img src="${posterUrl}" alt="Movie Poster" class="modal-poster" />
          <h2>${movie.title}</h2>
          <p>${movie.overview || 'No description available.'}</p>

          <button id="commentBtn" class="comment-btn">Comment</button>
        </div>
      </div>
    `;

    movieModal.style.display = 'block';

    document.getElementById('closeMovieModal').onclick = () => {
      movieModal.style.display = 'none';
    };

    document.getElementById('commentBtn').onclick = () => {
      window.location.href = `comments.html?movieId=${movie.id}&title=${encodeURIComponent(movie.title)}`;
    };
  }

  window.addEventListener('click', (e) => {
    const movieModal = document.getElementById('movieModal');
    if (e.target === movieModal) {
      movieModal.style.display = 'none';
    }
  });

});