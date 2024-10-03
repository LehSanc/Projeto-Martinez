<?php
include("conexao.php");

// Caso exista uma sessão ela é destruída para que o login seja feito corretamente
if (isset($_SESSION)) session_destroy();
session_start();
$mensagem = [];

// Perceba que um usuário pode ser encontrado no sistema mas não pode acessar o sistema por ter o ID = 0 ou NULL
function verificaUsername($username)
{
  global $mensagem, $mysqli;

  $stmt = $mysqli->prepare("SELECT * FROM `usuarios` WHERE `username`='$username'"); // Preparar a consulta
  if ($stmt === false) {
    die("Erro na preparação: " . $mysqli->error);
  }

  $stmt->execute(); //executar consulta
  $result = $stmt->get_result(); //obter resultado


  if ($result->num_rows > 0) {
    return true;
  } else {
    $mensagem[] = "Usuário não cadastrado no sistema.";
    return false;
  }
  $stmt->close(); //fechar declaração
}

// O ID representa o tipo de usuário: 
// Caso seja 0 ou NULL o usuário já fez uma solicitação de cadastro mas o ADM não recusou e nem aceitou
// ID = 1 representa um usuário comum
// ID = 2 representa um ADM 
function userValido($ID)
{
  global $mensagem;
  if ($ID == NULL) {
    $mensagem[] = "Sua solicitação de acesso foi recebida mas ainda não foi aceita.";
    return false;
  } else return true;
}

if (isset($_POST['senha']) && isset($_POST['username'])) {

  $username = strtolower(trim($_POST['username']));
  $senha = trim($_POST['senha']);

  if (verificaUsername($username)) {

    $stmt = $mysqli->prepare("SELECT `ID`, `senha` FROM `usuarios` WHERE `username` = '$username'"); // Preparar a consulta
    if ($stmt === false) {
      die("Erro na preparação: " . $mysqli->error);
    }
    $stmt->execute(); //executar consulta
    $result = $stmt->get_result(); //obter resultado
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc(); //obter os dados do usuario
      $ID = $row['ID'];

      if (password_verify($senha, $row['senha'])) {
        if (userValido($ID)) {
          $_SESSION['username'] = $username;

          $caminho = "./user/menu.php";

          if ($ID == 2) $caminho = "./ADM/ADMmenu.php";

          header("Location: $caminho");
          exit();
        }
      } else {
        $mensagem[] = "Senha incorreta";
      }
    }
    $stmt->close(); //fechar declaração

  }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/index.css" class="css">
  <link rel="stylesheet" href="css/responsive.css" media="screen">

  <!-- Google fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <!-- /Google fonts -->

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  <!-- /Bootstrap -->

  <!-- scripts de js -->
  <script src="js/formularioLogin.js" defer></script>
  <title>Tela inicial</title>
</head>

<body>

  <section class="hero-site">
    <div class="interface">

      <div aria-live="polite" aria-atomic="true">
        <div class="toast-container p-3">

          <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              <strong class="me-auto">Martinez</strong>
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

      <div class="colunaStart">

        <div class="interface">
          <div class="card text-center">
            <div class="card-header">
              <div class="fs-2 fw-semibold vermelho">Martinez</div>
            </div>
            <div class="card-body">
              <div class="d-flex flex-column">
                <div class="pt-2 fs-3 fw-light">corporation</div>
                <div class="pb-1 fs-5 fw-light">Turning problems into performance</div>
              </div>
            </div>
            <div class="card-footer text-body-secondary">
            </div>
          </div>
        </div>

      </div>

      <div class="d-flex flex-row-reverse mb-5">
        <button type="button" class="btn btn-light btn-lg" id="mostrarFormulario" onclick="mostrarFormulario()">
          <div class="d-flex flex-row">
            <div class="pe-2">Entrar</div>
            <div class="ps-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z" />
              </svg>
            </div>
          </div>
        </button>
      </div>

    </div>

    <div class="formulario" id="formulario">
      <form method="POST" action="" class="p-4">

        <div class="text-end mb-5">
          <button type="button" class="btn-close" aria-label="Close" onclick="hideFormulario()"></button>
        </div>

        <div class="d-flex flex-column m-3">
          <div class="text-center mb-3">
            <div class="pt-2 fs-4 fw-light">Novo por aqui?</div>
            <div class="pt-2 fs-2 fw-semibold">
              <a href="./solicitarCadastro.php" target="_blank">Solicitar cadastro</a>
            </div>
          </div>

          <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <div class="input-group" id="username">
              <span class="input-group-text" id="username">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                  <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                </svg>
              </span>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
          </div>

          <div class="form-group">
            <label for="senha" class="form-label">Senha</label>
            <div class="input-group">
              <span class="input-group-text" id="senha">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                  <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56" />
                  <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415" />
                </svg>
              </span>
              <input type="password" class="form-control" id="senha" aria-describedby="senha" name="senha" required>
            </div>
          </div>
          <a href="./esqueciSenha.php" target="_blank">Esqueci a senha</a>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-outline-light btn-lg my-2" id="mostrarFormulario">Entrar</button>
          </div>
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

  <script>
    function mostrarFormulario() { // Função para abrir o form
      document.getElementById('formulario').style.display = 'block';
    }
  </script>

  <script>
    function hideFormulario() { // Função para fechar o form
      document.getElementById('formulario').style.display = 'none';
    }
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