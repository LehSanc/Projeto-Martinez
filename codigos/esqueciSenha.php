<?php
include("conexao.php");
if (!isset($_SESSION)) session_start();
$mensagem = [];

function verificarDados($username, $DNI, $passaporte)
{
  global $mensagem, $mysqli;

  $stmt = $mysqli->prepare("SELECT `DNI`, `passaporte` FROM `usuarios` WHERE `username`='$username' AND `ID` != '0'"); // Preparar a consulta
  if ($stmt === false) {
    die("Erro na preparação: " . $mysqli->error);
  }

  $stmt->execute(); //executar consulta
  $result = $stmt->get_result(); //obter resultado

  // Quando não é encontrado o DNI nem o passaporte isso significa:
  // O ADM recusou a solicitação de cadastro e portanto foi apagada da database
  // O usuário não fez uma solicitação de cadsatro
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); //obter os dados do usuario

    if ($row['DNI'] == $DNI) {

      if ($row['passaporte'] == $passaporte) {
        return true;
      } else {
        $mensagem[] = "Passaporte não cadastrado no sistema. Certifique-se de digitar a informação correta";
        return false;
      }
    } else {
      $mensagem[] = "DNI não cadastrado no sistema. Certifique-se de digitar a informação correta";
      return false;
    }
  } else {
    $mensagem[] = "Sua solicitação de cadastro não foi recebida ou não foi aceita.";
    return false;
  }
  $stmt->close(); //fechar declaração
}

if (isset($_POST['username']) && isset($_POST['DNI']) && isset($_POST['passaporte'])) {

  $username =  strtolower(trim($_POST['username']));
  $DNI =  strtolower(trim($_POST['DNI']));
  $passaporte =  strtolower(trim($_POST['passaporte']));

  if (verificarDados($username, $DNI, $passaporte)) {

    // Essa variável user é apenas para permitir que o usuário troque a senha
    // Ela é diferente da variável username que permite o acesso nas outras páginas
    // Ou seja, mesmo trocando a senha o usuário ainda precisa fazer o login 
    $_SESSION['user'] = $username;
    echo "<script>location.href = 'trocarSenha.php';</script>";
  }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/formPrimarios.css">
  <link rel="stylesheet" href="css/responsive.css" media="screen">

  <!-- Google fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!-- /Google fonts -->

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  <!-- /Bootstrap -->

  <!-- script de js -->
  <script src="./js/navbar.js" defer></script>
  <!-- /script de js -->

  <title>Recuperar senha</title>
</head>

<body>

  <header id="header">

    <nav class="navbar px-5 text-center fixed-top">
      <a class="navbar-brand" href="./index.php">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="24" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
          <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" />
        </svg>
      </a>
    </nav>

  </header>

  <section class="hero-site">
    <div class="interface">

      <div aria-live="polite" aria-atomic="true">
        <div class="toast-container p-3">

          <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              <strong class="me-auto">PDR</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
              <?php foreach ($mensagem as $msg) {
                echo $msg;
              } ?>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="overlay"></div>
  </section>

  <section class="formulario pt-4">
    <div class="interface">

      <div class="d-flex flex-column mb-3 text-center">
        <div class="pt-2 fs-1 fw-bold">Esqueceu a senha?</div>
        <div class="fs-3 fw-light vermelho">Não tem problema!</div>
        <div class="p-2 fs-5 fw-light">Por motivos de segurança, responda o formulário comprovando a autenticidade da autoria e altere sua senha!</div>
      </div>

      <form method="POST" action="" class="needs-validation row g-3" novalidate>

        <div class="col-sm-9 p-3 col-md-4">
          <label for="DNI" class="form-label">DNI</label>
          <div class="input-group" id="DNI">
            <span class="input-group-text" id="DNI">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-post" viewBox="0 0 16 16">
                <path d="M4 3.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5z" />
                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1" />
              </svg>
            </span>
            <input type="text" class="form-control" id="DNI" name="DNI" aria-describedby="DNI" required>
          </div>
        </div>

        <div class="col-sm-9 p-3 col-md-4">
          <label for="passaporte" class="form-label">Passaporte</label>
          <div class="input-group" id="passaporte">
            <span class="input-group-text" id="passaporte">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-post-fill" viewBox="0 0 16 16">
                <path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M4.5 3h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1m0 2h7a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5" />
              </svg>
            </span>
            <input type="text" class="form-control" id="passaporte" name="passaporte" aria-describedby="passaporte" required>
          </div>
        </div>

        <div class="col-sm-9 p-3 col-md-4">
          <label for="username" class="form-label">Username</label>
          <div class="input-group" id="username">
            <span class="input-group-text" id="username">@</span>
            <input type="text" class="form-control" id="username" name="username" aria-describedby="username" required>
          </div>
        </div>

        <div class="p-3 col-12 text-end">
          <button type="submit" class="btn btn-outline-dark btn-lg my-3">Enviar</button>
        </div>

      </form>

    </div>
  </section>

  <script>
    (() => {
      'use strict'

      const forms = document.querySelectorAll('.needs-validation')

      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }

          form.classList.add('was-validated')
        }, false)
      })
    })()
  </script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      <?php
      if (count($mensagem) > 0) {
      ?>
        var toastEl = document.querySelector('.toast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
      <?php
      }
      ?>
    });
  </script>

</body>

</html>