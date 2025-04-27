// Main Movie App
document.addEventListener('DOMContentLoaded', () => {
  const API_KEY = '4fd186b10fe65f080443247342f9cc5c';
  const BASE_URL = 'https://api.themoviedb.org/3';

  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchBtn');
  const categoryDropdown = document.getElementById('categoryDropdown');
  const moviesDiv = document.getElementById('movies');
  const heroSection = document.querySelector('.hero');

  let heroMovies = [];
  let heroIndex = 0;
  let heroTimer;

  // --- Fetch Hero Movies ---
  async function fetchHeroMovies() {
    try {
      const res = await fetch(`${BASE_URL}/trending/movie/day?api_key=${API_KEY}`);
      const data = await res.json();
      if (data.results.length > 0) {
        heroMovies = data.results.filter(movie => movie.backdrop_path);
        updateHeroBanner();
        startHeroCarousel();
      }
    } catch (err) {
      console.error('Failed to fetch hero movies:', err);
    }
  }

  // --- Fetch Genres for Category Dropdown ---
  async function fetchGenres() {
    try {
      const res = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}`);
      const data = await res.json();
      if (data.genres && data.genres.length > 0) {
        categoryDropdown.innerHTML = '<option value="">All Categories</option>';
        data.genres.forEach(genre => {
          const option = document.createElement('option');
          option.value = genre.id;
          option.textContent = genre.name;
          categoryDropdown.appendChild(option);
        });
      }
    } catch (err) {
      console.error('Failed to fetch genres:', err);
    }
  }

  // --- Fetch Movies based on Search + Category ---
  async function fetchMovies(query = '', genreId = '') {
    try {
      let url;
      if (query) {
        url = `${BASE_URL}/search/movie?api_key=${API_KEY}&query=${encodeURIComponent(query)}`;
      } else if (genreId) {
        url = `${BASE_URL}/discover/movie?api_key=${API_KEY}&with_genres=${genreId}`;
      } else {
        url = `${BASE_URL}/trending/movie/day?api_key=${API_KEY}`;
      }

      const res = await fetch(url);
      const data = await res.json();
      if (data.results.length > 0) {
        let filteredMovies = data.results;
        if (query && genreId) {
          filteredMovies = data.results.filter(movie => movie.genre_ids.includes(parseInt(genreId)));
        }

        moviesDiv.innerHTML = filteredMovies.map(movieCardHTML).join('');
        document.querySelectorAll('.movie-card').forEach((card, index) => {
          card.addEventListener('click', () => {
            const movie = filteredMovies[index];
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
      } else {
        moviesDiv.innerHTML = `<p class="error">No movies found.</p>`;
      }
    } catch (err) {
      console.error('Failed to fetch movies:', err);
      moviesDiv.innerHTML = `<p class="error">Error loading movies. Please try again later.</p>`;
    }
  }

  // --- Movie Card Template ---
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
          <p><strong>Rating:</strong> ${movie.vote_average ? movie.vote_average.toFixed(1) : 'N/A'} / 10</p>
          <p>${movie.overview || 'No description available.'}</p>
        </div>
      </div>
    `;
  }

  // --- Open Movie Modal ---
  function openMovieModal(movie) {
    const movieModal = document.getElementById('movieModal');
    if (!movieModal) return;

    const posterUrl = movie.posterPath
      ? `https://image.tmdb.org/t/p/w500${movie.posterPath}`
      : 'https://via.placeholder.com/200x300?text=No+Image';

    movieModal.innerHTML = `
      <div class="modal-content movie-modal">
        <span class="close-button" id="closeMovieModal">&times;</span>
        <div class="modal-body" style="display: flex; flex-wrap: wrap; gap: 20px;">
          <div class="fake-player" style="flex: 2 1 600px;">
            <div class="play-icon">&#9658;</div>
          </div>
          <div class="comment-section" style="flex: 1 1 300px; background:#111; padding: 15px; border-radius:8px; overflow-y:auto; max-height:600px;">
            <h3 style="margin-bottom: 10px; color: #e50914;">Comments</h3>
            <div id="commentList" style="margin-bottom: 15px;"><p>No comments yet. Be the first!</p></div>
            <textarea id="newComment" placeholder="Write a comment..." style="width:100%; padding:8px; border-radius:6px; background:#222; color:#eee; border:none; margin-bottom:10px; resize:none;"></textarea>
            <button id="submitComment" class="comment-btn" style="width: 100%;">Submit</button>
          </div>
        </div>
        <div class="movie-details" style="text-align: center; margin-top: 20px;">
          <img src="${posterUrl}" alt="Movie Poster" class="modal-poster" />
          <h2>${movie.title}</h2>
          <p>${movie.overview || 'No description available.'}</p>
        </div>
      </div>
    `;

    movieModal.style.display = 'block';

    const closeButton = document.getElementById('closeMovieModal');
    if (closeButton) {
      closeButton.onclick = () => movieModal.style.display = 'none';
    }

    document.getElementById('submitComment').onclick = () => {
      const newCommentText = document.getElementById('newComment').value.trim();
      if (newCommentText) {
        const commentList = document.getElementById('commentList');
        const newP = document.createElement('p');
        newP.textContent = newCommentText;
        commentList.appendChild(newP);
        document.getElementById('newComment').value = '';
      }
    };

    document.querySelector('.fake-player').addEventListener('click', () => {
      window.location.href = 'maintenance.html';
    });
  }

  // --- Hero Banner ---
  function updateHeroBanner() {
    if (!heroMovies.length || !heroSection) return;
    const movie = heroMovies[heroIndex];
    const backgroundUrl = movie.backdrop_path
      ? `https://image.tmdb.org/t/p/original${movie.backdrop_path}`
      : 'https://via.placeholder.com/1600x900?text=No+Image';

    heroSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('${backgroundUrl}')`;

    heroSection.classList.remove('fade-in');
    void heroSection.offsetWidth;
    heroSection.classList.add('fade-in');

    heroSection.innerHTML = `
      <div class="hero-content">
        <h1>${movie.title}</h1>
        <button id="heroPlayBtn" class="hero-play-btn">Learn more</button>
      </div>
    `;

    const playButton = document.getElementById('heroPlayBtn');
    playButton.onclick = () => {
      openMovieModal({
        title: movie.title,
        id: movie.id,
        posterPath: movie.poster_path,
        overview: movie.overview
      });
    };
  }

  function startHeroCarousel() {
    if (heroTimer) clearInterval(heroTimer);
    heroTimer = setInterval(() => {
      heroIndex = (heroIndex + 1) % heroMovies.length;
      updateHeroBanner();
    }, 7000);
  }

  // --- Save Search History ---
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

  // --- Handle Search Submit ---
  if (searchBtn) {
    searchBtn.addEventListener('click', () => {
      const query = searchInput.value.trim();
      const genreId = categoryDropdown.value;
      fetchMovies(query, genreId);
    });
  }

  // --- ESC Key Close Modal ---
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const movieModal = document.getElementById('movieModal');
      if (movieModal && movieModal.style.display === 'block') {
        movieModal.style.display = 'none';
      }
    }
  });

  // --- Load Everything ---
  fetchHeroMovies();
  fetchGenres();
  fetchMovies();
});

// --- Mobile Navigation Toggle ---
document.addEventListener('DOMContentLoaded', () => {
  const hamburger = document.getElementById('hamburger');
  const navLinks = document.getElementById('navLinks');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      navLinks.classList.toggle('show');
    });
  }
});
