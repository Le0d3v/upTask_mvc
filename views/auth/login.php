<div class="contenedor login">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <?php include_once __DIR__ . "/../templates/nombre-sitio.php"?>
  <div class="contenedor-sm">
    <p class="descripcion-pagina">Iniciar Sesión</p>
    <?php include_once __DIR__ . "/../templates/alertas.php"?>
    <form action="/" method="post" class="formulario" >
      <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Tu email" name="email">
      </div>
      <div class="campo">
        <label for="password">Password</label> 
        <input type="password" id="password" placeholder="Tu password" name="password">
      </div>
      <div class="g-recaptcha" data-sitekey="6LdIHb4qAAAAAPNATSDmc0kBbSE79l2Im8pmf94Z"></div>
      <input type="submit" class="boton" value="Iniciar Sesión">
    </form>
    <div class="acciones">
      <a href="/crear">¿Aun no tienes una cuenta? Crear Una</a>
      <a href="/olvide">¿Olvidaste tu password?</a>
    </div>
  </div>
</div>
