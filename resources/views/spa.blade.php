<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>ArenaGaming - Mini Catálogo</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="text-gray-900 bg-gray-100">
    <div id="app" class="flex flex-col min-h-screen">
      <header class="bg-white shadow">
        <div class="flex items-center justify-between max-w-6xl p-4 mx-auto">
          <h1 class="text-2xl font-semibold">ArenaGaming</h1>
          <nav class="space-x-2">
            <button id="btn-show-login" class="px-3 py-1 text-white bg-blue-600 rounded">Login</button>
            <button id="btn-show-register" class="px-3 py-1 text-white bg-green-600 rounded">Registro</button>
            <button id="btn-logout" class="hidden px-3 py-1 text-white bg-red-500 rounded">Logout</button>
          </nav>
        </div>
      </header>

      <main class="flex-1 max-w-6xl p-4 mx-auto">
        <section id="external-block" class="p-4 mb-6 bg-white rounded shadow">
          <div id="external-content" class="text-sm text-gray-700">Cargando...</div>
        </section>

        <!-- Sección de Favoritos (solo para usuarios autenticados) -->
        <section id="favorites-block" class="hidden p-4 mb-6 bg-gradient-to-r from-yellow-100 to-amber-50 rounded-lg shadow border border-yellow-400">
          <h2 class="text-lg font-semibold text-yellow-900 mb-3">⭐ Mis Favoritos</h2>
          <div id="favorites-section" class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-5">
            <!-- Se llenará con JS -->
          </div>
        </section>

        <section class="mb-6">
          <h2 class="text-2xl font-bold mb-4 text-gray-800">📦 Catálogo Completo</h2>
          <div id="catalog-section" class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <!-- Catalogo: se llenará con JS -->
          </div>
        </section>
        <div id="catalog-pagination" class="mt-6 text-center space-x-2"></div>

        <section id="detail-modal" class="fixed inset-0 flex items-center justify-center hidden p-4 bg-black/50">
          <div class="relative w-full max-w-2xl p-6 bg-white rounded-lg shadow-xl max-h-[90vh] overflow-y-auto">
            <button id="close-detail" class="absolute text-gray-500 hover:text-gray-700 right-4 top-4 text-xl">✕</button>
            <div id="detail-content"></div>
            
            <!-- Sección de Comentarios -->
            <div class="mt-6 pt-4 border-t">
              <h3 class="text-lg font-semibold mb-3">💬 Comentarios</h3>
              <div id="comments-list" class="mt-3 space-y-3 max-h-60 overflow-y-auto"></div>
              
              <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <label for="comment-input" class="block text-sm font-medium text-gray-700 mb-2">Deja tu comentario</label>
                <textarea 
                  id="comment-input" 
                  maxlength="200" 
                  placeholder="Escribe tu opinión sobre este juego (máx. 200 caracteres)..." 
                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                  rows="3"
                ></textarea>
                <div class="flex justify-between items-center mt-2">
                  <span id="char-count" class="text-xs text-gray-500">0/200 caracteres</span>
                  <button id="send-comment" class="px-4 py-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium">
                    Enviar comentario
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Login/Register modals -->
        <section id="auth-modal" class="fixed inset-0 items-center justify-center hidden p-4 bg-black/50">
          <div class="w-full max-w-md p-4 bg-white rounded shadow">
            <button id="close-auth" class="absolute text-gray-500 right-3 top-3">Cerrar</button>
            <div id="auth-forms">
              <form id="login-form" class="space-y-2">
                <h3 class="font-semibold">Iniciar sesión</h3>
                <input id="login-email" type="email" placeholder="Correo" required class="w-full p-2 border rounded" />
                <input id="login-password" type="password" placeholder="Contraseña" required class="w-full p-2 border rounded" />
                <button class="px-3 py-1 text-white bg-blue-600 rounded">Entrar</button>
              </form>

              <form id="register-form" class="hidden space-y-2">
                <h3 class="font-semibold">Registro</h3>
                <input id="reg-name" type="text" placeholder="Nombre" required class="w-full p-2 border rounded" />
                <input id="reg-email" type="email" placeholder="Correo" required class="w-full p-2 border rounded" />
                <input id="reg-password" type="password" placeholder="Contraseña" required class="w-full p-2 border rounded" />
                <button class="px-3 py-1 text-white bg-green-600 rounded">Registrar</button>
              </form>
            </div>
          </div>
        </section>

      </main>
      <footer class="p-4 text-sm text-center bg-white border-t">ArenaGaming &copy; 2025</footer>
    </div>
  </body>
</html>
