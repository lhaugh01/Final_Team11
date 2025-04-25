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
      if (data.results && data.results.length > 0) {
        displayMovies(data.results);
      } else {
        moviesDiv.innerHTML = `<p class="error">No movies found for "${query}".</p>`;
      }
    } catch (err) {
      console.error('Failed to fetch movies:', err);
      moviesDiv.innerHTML = `<p class="error">Error loading movies. Please try again later.</p>`;
    }
  }

  function displayMovies(movies) {
    moviesDiv.innerHTML = '';
    movies.forEach(movie => {
      const movieDiv = document.createElement('div');
      movieDiv.className = 'movie-card';

      const posterPath = movie.poster_path
        ? `https://image.tmdb.org/t/p/w300${movie.poster_path}`
        : 'https://via.placeholder.com/200x300?text=No+Image';

      movieDiv.innerHTML = `
        <img src="${posterPath}" alt="${movie.title}" class="poster">
        <div class="movie-info">
          <h3>${movie.title}</h3>
          <p><strong>Release:</strong> ${movie.release_date || 'N/A'}</p>
          <p><strong>Popularity:</strong> ${movie.popularity}</p>
          <p>${movie.overview}</p>
        </div>
      `;

      // 🔥 Add click listener to record history
      movieDiv.addEventListener('click', () => {
        updateSearchHistory(movie.title);
      });

      moviesDiv.appendChild(movieDiv);
    });
  }

  function getQueryAndSort() {
    const query = searchInput.value.trim() || 'avengers';
    const sort = sortOptions.value;
    return { query, sort };
  }

  searchInput.addEventListener('input', () => {
    const { query, sort } = getQueryAndSort();
    fetchMovies(query, sort);
  });

  sortOptions.addEventListener('change', () => {
    const { query, sort } = getQueryAndSort();
    fetchMovies(query, sort);
  });

  searchBtn.addEventListener('click', () => {
    const { query, sort } = getQueryAndSort();
    fetchMovies(query, sort);
  });

  fetchMovies();

  async function fetchUserData() {
    try {
      const response = await fetch('http://localhost:3001/api/users');
      const data = await response.json();
      console.log('Users from MySQL:', data);

      data.forEach(user => {
        console.log(`Name: ${user.name}, Email: ${user.email || 'No email provided'}`);
      });

    } catch (error) {
      console.error('Error fetching user data:', error);
    }
  }

  async function updateSearchHistory(movieTitle) {
    try {
      const userId = localStorage.getItem('userId');
      if (!userId) return;

      await fetch('http://localhost:3001/api/update-history', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId, movieTitle })
      });
    } catch (err) {
      console.error('Failed to update search history:', err);
    }
  }

  fetchUserData();

  // login
  const loginModal = document.getElementById('loginModal');
  const loginTrigger = document.getElementById('loginTrigger');
  const closeLogin = document.getElementById('closeLogin');

  loginTrigger.addEventListener('click', () => {
    loginModal.style.display = 'block';
  });

  closeLogin.addEventListener('click', () => {
    loginModal.style.display = 'none';
  });

  window.addEventListener('click', (e) => {
    if (e.target === loginModal) {
      loginModal.style.display = 'none';
    }
  });

  document.getElementById('loginForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const username = document.getElementById('username').value;
    alert(`Welcome, ${username}!`);
    loginModal.style.display = 'none';
    localStorage.setItem('userId', '1'); // 🔧 Replace '1' with actual ID from server logic
  });

  // signup
  const signupModal = document.getElementById('signupModal');
  const signupTrigger = document.getElementById('signupTrigger');
  const closeSignup = document.getElementById('closeSignup');

  signupTrigger.addEventListener('click', () => {
    signupModal.style.display = 'block';
  });

  closeSignup.addEventListener('click', () => {
    signupModal.style.display = 'none';
  });

  window.addEventListener('click', (e) => {
    if (e.target === signupModal) {
      signupModal.style.display = 'none';
    }
  });

  document.getElementById('signupForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const newUser = document.getElementById('newUsername').value;
    alert(`Thanks for signing up, ${newUser}!`);
    signupModal.style.display = 'none';
  });
});