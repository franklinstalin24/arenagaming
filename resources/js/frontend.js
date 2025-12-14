// Frontend logic for SPA (minimal, no frameworks)
// This file will be imported by resources/js/app.js

// Firebase imports comentados - descomenta si lo necesitas para upload de imágenes
// import { initializeApp } from 'https://www.gstatic.com/firebasejs/9.24.0/firebase-app.js';
// import { getStorage, ref as storageRef, uploadBytes, getDownloadURL } from 'https://www.gstatic.com/firebasejs/9.24.0/firebase-storage.js';

const API_BASE = '/api';

const state = {
  token: localStorage.getItem('token') || null,
};

function qs(sel) { return document.querySelector(sel); }

function qsa(sel) { return document.querySelectorAll(sel); }

async function fetchExternal() {
  const el = qs('#external-content');
  el.textContent = 'Cargando clima...';
  try {
    const res = await fetch('/api/weather?city=Quito');
    const data = await res.json();
    if (data && data.success && data.text) {
      el.textContent = data.text;
    } else {
      el.textContent = data.message || 'Configura WEATHER_API_KEY en .env (usa OpenWeatherMap gratis)';
    }
  } catch (e) {
    el.textContent = 'Error de conexión con API externa.';
  }
}

async function loadCatalog() {
  const container = qs('#catalog-section');
  container.innerHTML = 'Cargando...';
  let json = null; // Declarar fuera del try
  try {
    const res = await fetch(`${API_BASE}/products`);
    json = await res.json();
    const items = Array.isArray(json?.data) ? json.data : [];
    container.innerHTML = '';
    items.forEach(p => {
      const card = document.createElement('div');
      card.className = 'p-4 bg-white rounded shadow';
      card.innerHTML = `
        <img src="${p.imagen_url || ''}" alt="${p.titulo || ''}" class="w-full h-48 object-cover rounded" />
        <h3 class="mt-2 font-semibold">${p.titulo || ''}</h3>
        <p class="text-sm text-gray-600">${p.descripcion_corta || ''}</p>
        <div class="mt-2 flex justify-between items-center">
          <strong>${(p.precio_oferta ?? p.precio_normal) ?? ''}</strong>
          <div>
            <button data-id="${p.id}" class="btn-detail px-2 py-1 bg-gray-200 rounded">Ver</button>
            <button data-id="${p.id}" class="btn-fav px-2 py-1 ml-2 bg-yellow-300 rounded">♥</button>
          </div>
        </div>
      `;
      container.appendChild(card);
    });

    // attach events
    qsa('.btn-detail').forEach(b => b.addEventListener('click', e => showDetail(e.target.dataset.id)));
    qsa('.btn-fav').forEach(b => b.addEventListener('click', e => toggleFav(e.target.dataset.id)));
  } catch (e) {
    container.innerHTML = 'Error cargando catálogo.';
  }

  // Render pagination if available
  const pag = json?.pagination;
  const pagEl = qs('#catalog-pagination');
  if (pag && pagEl) {
    pagEl.innerHTML = '';
    for (let p = 1; p <= (pag.last_page || 1); p++) {
      const btn = document.createElement('button');
      btn.textContent = p;
      btn.className = `px-2 py-1 m-1 rounded ${p === pag.current_page ? 'bg-blue-600 text-white' : 'bg-gray-200'}`;
      btn.onclick = async () => {
        const res2 = await fetch(`${API_BASE}/products?page=${p}`);
        const json2 = await res2.json();
        const items2 = Array.isArray(json2?.data) ? json2.data : [];
        container.innerHTML = '';
        items2.forEach(pp => {
          const card = document.createElement('div');
          card.className = 'p-4 bg-white rounded shadow';
          card.innerHTML = `
            <img src="${pp.imagen_url || ''}" alt="${pp.titulo || ''}" class="w-full h-48 object-cover rounded" />
            <h3 class="mt-2 font-semibold">${pp.titulo || ''}</h3>
            <p class="text-sm text-gray-600">${pp.descripcion_corta || ''}</p>
            <div class="mt-2 flex justify-between items-center">
              <strong>${(pp.precio_oferta ?? pp.precio_normal) ?? ''}</strong>
              <div>
                <button data-id="${pp.id}" class="btn-detail px-2 py-1 bg-gray-200 rounded">Ver</button>
                <button data-id="${pp.id}" class="btn-fav px-2 py-1 ml-2 bg-yellow-300 rounded">♥</button>
              </div>
            </div>
          `;
          container.appendChild(card);
        });
        qsa('.btn-detail').forEach(b => b.addEventListener('click', e => showDetail(e.target.dataset.id)));
        qsa('.btn-fav').forEach(b => b.addEventListener('click', e => toggleFav(e.target.dataset.id)));
      };
      pagEl.appendChild(btn);
    }
  }
}

async function showDetail(id) {
  try {
    const res = await fetch(`${API_BASE}/products/${id}`);
    const json = await res.json();
    const p = json?.data || {};
    const detail = qs('#detail-content');
    detail.innerHTML = `
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <img src="${p.imagen_url || ''}" alt="${p.titulo || ''}" class="w-full h-64 object-cover rounded" />
        <div>
          <h2 class="text-xl font-bold">${p.titulo || ''}</h2>
          <p class="mt-2 text-sm text-gray-700">${p.descripcion_larga || p.descripcion_corta || ''}</p>
          <p class="mt-2 font-semibold">Precio: ${(p.precio_oferta ?? p.precio_normal) ?? ''}</p>
        </div>
      </div>
    `;
    qs('#detail-modal').classList.remove('hidden');
    loadComments(id);
    
    // Setup comment input character counter
    const commentInput = qs('#comment-input');
    const charCount = qs('#char-count');
    commentInput.value = '';
    charCount.textContent = '0/200 caracteres';
    
    commentInput.addEventListener('input', function() {
      const length = this.value.length;
      charCount.textContent = `${length}/200 caracteres`;
      if (length >= 200) {
        charCount.classList.add('text-red-500');
        charCount.classList.remove('text-gray-500');
      } else {
        charCount.classList.add('text-gray-500');
        charCount.classList.remove('text-red-500');
      }
    });
    
    qs('#send-comment').onclick = () => sendComment(id);
  } catch (e) {
    console.error('Error loading detail:', e);
    alert('Error al cargar detalle');
  }
}

function closeDetail() { qs('#detail-modal').classList.add('hidden'); }

async function loadComments(juegoId) {
  const list = qs('#comments-list');
  list.innerHTML = '<p class="text-sm text-gray-500">Cargando comentarios...</p>';
  try {
    const res = await fetch(`${API_BASE}/comments/${juegoId}`);
    
    if (!res.ok) {
      throw new Error('Error en la respuesta del servidor');
    }
    
    const data = await res.json();
    
    if (!Array.isArray(data) || data.length === 0) {
      list.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">📭 No hay comentarios aún. ¡Sé el primero en comentar!</p>';
      return;
    }
    
    list.innerHTML = data.map(c => `
      <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition">
        <div class="flex justify-between items-start mb-1">
          <strong class="text-sm text-blue-700">${c.user_name || 'Usuario'}</strong>
          <span class="text-xs text-gray-400">${c.created_at || ''}</span>
        </div>
        <p class="text-sm text-gray-700">${c.comment || ''}</p>
      </div>
    `).join('');
  } catch (e) { 
    console.error('Error loading comments:', e);
    list.innerHTML = '<p class="text-sm text-red-500 text-center py-4">⚠️ Error al cargar comentarios. Por favor intenta de nuevo.</p>';
  }
}

async function sendComment(juegoId) {
  const input = qs('#comment-input');
  const text = input.value.trim();
  
  if (!text) {
    alert('Por favor escribe un comentario');
    return;
  }
  
  if (text.length > 200) {
    alert('El comentario no puede exceder 200 caracteres');
    return;
  }
  
  if (!state.token) {
    alert('Debes iniciar sesión para comentar');
    return;
  }
  
  try {
    const res = await fetch(`${API_BASE}/comments`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${state.token}`
      },
      body: JSON.stringify({ juego_id: juegoId, comment: text })
    });
    
    if (!res.ok) {
      const errData = await res.json();
      throw new Error(errData.message || 'Error al enviar comentario');
    }
    
    input.value = '';
    alert('Comentario enviado correctamente');
    loadComments(juegoId);
  } catch (e) { 
    console.error('Error sending comment:', e);
    alert(e.message || 'Error al enviar comentario. ¿Estás autenticado?');
  }
}

async function loadFavorites() {
  if (!state.token) {
    // Si no está autenticado, ocultar la sección
    const block = qs('#favorites-block');
    if (block) block.classList.add('hidden');
    return;
  }
  
  const container = qs('#favorites-section');
  const block = qs('#favorites-block');
  
  // Siempre mostrar la sección cuando el usuario está autenticado
  block.classList.remove('hidden');
  
  try {
    const res = await fetch(`${API_BASE}/favorites`, {
      headers: { 'Authorization': `Bearer ${state.token}` }
    });
    
    if (!res.ok) {
      throw new Error('Error al cargar favoritos');
    }
    
    const json = await res.json();
    const items = Array.isArray(json?.data) ? json.data : [];
    
    container.innerHTML = '';
    
    if (items.length === 0) {
      // Mostrar mensaje cuando no hay favoritos
      container.innerHTML = `
        <div class="col-span-full p-4 text-center text-gray-500">
          <p class="text-sm">⭐ Aún no tienes favoritos</p>
          <p class="text-xs mt-1">Haz clic en ♥ para agregar juegos</p>
        </div>
      `;
      return;
    }
    
    items.forEach(fav => {
      const p = fav.juego || {};
      const card = document.createElement('div');
      card.className = 'p-2 bg-white rounded shadow border border-yellow-300';
      card.innerHTML = `
        <img src="${p.imagen_url || ''}" alt="${p.titulo || ''}" class="w-full h-24 object-cover rounded" />
        <h3 class="mt-1 text-xs font-semibold truncate">${p.titulo || ''}</h3>
        <div class="mt-1 flex justify-between items-center">
          <strong class="text-xs">${(p.precio_oferta ?? p.precio_normal) ?? ''}</strong>
          <button data-id="${p.id}" class="btn-unfav px-1 py-0.5 text-xs bg-red-500 text-white rounded">✕</button>
        </div>
      `;
      container.appendChild(card);
    });
    
    // Attach remove from favorites events
    qsa('.btn-unfav').forEach(b => {
      b.addEventListener('click', e => {
        const id = e.target.dataset.id;
        fetch(`${API_BASE}/favorites/${id}`, {
          method: 'DELETE',
          headers: { 'Authorization': `Bearer ${state.token}` }
        }).then(() => {
          loadFavorites(); // Recargar la lista
          loadCatalog(); // Actualizar catálogo para reflejar cambios
          alert('Removido de favoritos');
        }).catch(() => alert('Error al remover'));
      });
    });
  } catch (e) {
    console.error('Error cargando favoritos:', e);
    container.innerHTML = `
      <div class="col-span-full p-4 text-center text-red-500">
        <p class="text-sm">Error al cargar favoritos</p>
      </div>
    `;
  }
}

async function toggleFav(juegoId) {
  if (!state.token) {
    alert('Debes iniciar sesión para agregar favoritos');
    return;
  }
  
  try {
    const res = await fetch(`${API_BASE}/favorites`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${state.token}` 
      },
      body: JSON.stringify({ juego_id: juegoId })
    });
    
    if (!res.ok) {
      const errData = await res.json();
      // Si ya existe, lo quitamos
      if (errData.message && errData.message.includes('already')) {
        const delRes = await fetch(`${API_BASE}/favorites/${juegoId}`, {
          method: 'DELETE',
          headers: { 'Authorization': `Bearer ${state.token}` }
        });
        if (delRes.ok) {
          loadFavorites();
          loadCatalog();
          alert('Removido de favoritos');
        }
      } else {
        alert(errData.message || 'Error al agregar a favoritos');
      }
      return;
    }
    
    alert('Agregado a favoritos!');
    loadFavorites(); // Recargar sección de favoritos
    loadCatalog(); // Recargar catálogo
  } catch (e) {
    console.error('Error toggling fav:', e);
    alert('Error al actualizar favoritos');
  }
}




// Auth forms
function setupAuth() {
  console.log('setupAuth iniciado');
  const loginBtn = qs('#btn-show-login');
  const registerBtn = qs('#btn-show-register');
  const authModal = qs('#auth-modal');
  
  console.log('loginBtn:', loginBtn);
  console.log('registerBtn:', registerBtn);
  console.log('authModal:', authModal);
  
  if (!loginBtn || !registerBtn || !authModal) {
    console.error('No se encontraron los elementos del modal');
    return;
  }
  
  loginBtn.onclick = () => { 
    console.log('Click login');
    authModal.classList.remove('hidden'); 
    qs('#login-form').classList.remove('hidden'); 
    qs('#register-form').classList.add('hidden'); 
  };
  registerBtn.onclick = () => { 
    console.log('Click register');
    authModal.classList.remove('hidden'); 
    qs('#login-form').classList.add('hidden'); 
    qs('#register-form').classList.remove('hidden'); 
  };
  qs('#close-auth').onclick = () => {
    console.log('Click close');
    authModal.classList.add('hidden');
  };
  qs('#close-detail').onclick = closeDetail;

  qs('#login-form').onsubmit = async (e) => {
    e.preventDefault();
    const email = qs('#login-email').value; const password = qs('#login-password').value;
    try {
      const res = await fetch(`${API_BASE}/auth/login`, {
        method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({email, password})
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Credenciales incorrectas');
        return;
      }
      state.token = data.data.token; 
      localStorage.setItem('token', state.token);
      qs('#auth-modal').classList.add('hidden'); 
      qs('#btn-logout').classList.remove('hidden');
      alert('Sesión iniciada correctamente');
      loadCatalog();
      loadFavorites(); // Cargar favoritos cuando inicia sesión
    } catch (e) { 
      console.error('Error login:', e);
      alert('Error de conexión'); 
    }
  };

  qs('#register-form').onsubmit = async (e) => {
    e.preventDefault();
    const name = qs('#reg-name').value; const email = qs('#reg-email').value; const password = qs('#reg-password').value;
    try {
      const res = await fetch(`${API_BASE}/auth/register`, {
        method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({name,email,password})
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Error en el registro');
        return;
      }
      alert('¡Registrado exitosamente! Ahora puedes iniciar sesión.');
      qs('#register-form').classList.add('hidden'); qs('#login-form').classList.remove('hidden');
    } catch (e) { alert('Error registro'); }
  };

  qs('#btn-logout').onclick = async () => { localStorage.removeItem('token'); state.token = null; qs('#btn-logout').classList.add('hidden'); alert('Cerraste sesión'); };
}

// Initialize firebase (expects FIREBASE config object in window.FIREBASE_CONFIG set by server or .env)
// Comentado: descomenta si instalas Firebase via npm
function initFirebase() {
  // if (!window.FIREBASE_CONFIG) return;
  // const app = initializeApp(window.FIREBASE_CONFIG);
  // return getStorage(app);
  console.log('Firebase deshabilitado - instala via npm si lo necesitas');
}

// bootstrap
document.addEventListener('DOMContentLoaded', () => {
  console.log('DOMContentLoaded - iniciando setup');
  setupAuth();
  fetchExternal(); // Carga el clima en #external-content
  loadCatalog();
  initFirebase();
});

export {};
