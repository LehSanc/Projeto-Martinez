<?php
include("../protect.php");
include("../conexao.php");
protect();

$mensagem = [];

if ($_SESSION['sucesso'] == 1) {
  $mensagem[] = "Alteração realizada com sucesso!";
  $_SESSION['sucesso'] = 0;
}

function aplicarFiltro($filtro, $pesquisa)
{
  global $mensagem, $mysqli;

  if ($filtro == "mes") {
    $stmt = $mysqli->prepare("SELECT * FROM `servicos` WHERE MONTH(data) = '$pesquisa'"); // Preparar a consulta
  } else {
    $stmt = $mysqli->prepare("SELECT * FROM `servicos` WHERE $filtro = '$pesquisa'"); // Preparar a consulta
  }
  if ($stmt === false) {
    die("Erro na preparação: " . $mysqli->error);
  }

  $stmt->execute(); //executar consulta
  $result = $stmt->get_result(); //obter resultado

  if ($result->num_rows > 0) {
    return $result;
  } else {
    $mensagem[] = "Não foi encontrada nenhuma correspondência no sistema.";
    return false;
  }
  $stmt->close();
}

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

function verificarMES($mes)
{
  global $mensagem;
  if ((is_numeric($mes) && $mes < 13 && $mes > 0)) {
    return true;
  } else {
    $mensagem[] = "Por favor, digite um mês válido.";
    return false;
  }
}

if (isset($_POST['filtro']) && isset($_POST['pesquisa'])) {
  global $mensagem;
  $filtro = $_POST['filtro'];
  $pesquisa = strtolower($_POST['pesquisa']);


  if ($filtro == "username" && verificaUsername($pesquisa)) {
    $result = aplicarFiltro($filtro, $pesquisa);
  } else if ($filtro == "mes" && verificarMES($pesquisa)) {
    $result = aplicarFiltro($filtro, $pesquisa);
  } else if ($filtro != "username" && $filtro != "mes") {
    $result = aplicarFiltro($filtro, $pesquisa);
  }
}

if (isset($_POST['editar_btn'])) {
  $_SESSION['ID_servico'] = $_POST['ID_servico'];
  header('Location: ADMeditar.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/menuEditar.css">
  <link rel="stylesheet" href="../css/responsive.css" media="screen">

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

  <!-- scripts -->
  <script src="../js/modal-img.js"></script>
  <!-- /script -->

  <title>Menu editar</title>
</head>

<body>

  <header>
    <nav class="navbar fixed-top">
      <div class="container-fluid">
        <h2 class="navbar-brand">Martinez</h2>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
          <div class="offcanvas-header">
            <h4 class="fw-bold">Martinez</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3 nav-underline">

              <h4 class="fw-bold">Meus dados</h4>
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="./ADMmeuPerfil.php" title="Meu perfil">Meu perfil</a>
              </li>

              <h4 class="fw-bold">Ações</h4>
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="./ADMmenu.php" title="Menu">Menu</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="./ADMadicionar.php" title="Adicionar serviço">Adicionar</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="./ADMmenuEditar.php" title="Editar serviço">Editar</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="./ADMmenuPlanilha.php" title="Gerar planilha">Planilha</a>
              </li>

              <h4 class="fw-bold">Administração</h4>
              <li class="nav-item">
                <a class="nav-link" href="./adicionarUsers.php" title="Solicitações de cadastro">Solicitações de cadastro</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="./deleteUsers.php" title="Remover usuário">Remover usuário</a>
              </li>

              <li class="nav-item">
                <a class="nav-link fw-semibold" href="../sair.php" title="Sair">Sair</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <section class="hero-site">
    <div class="interface">

      <div aria-live="polite" aria-atomic="true" class="position-toast">
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

      <div class="card text-center">
        <div class="card-header">
          <div class="fs-2 fw-semibold vermelho">Menu editar</div>
          <div class="fs-5 fw-light">Encontre serviços adicionados e faça alterações!</div>
        </div>
        <div class="card-body">
          <form method="POST" class="needs-validation" action="" novalidate>
            <div class="row g-3">
              <div class="col-md-4">
                <div class="form-floating">
                  <select class="form-select" id="filtro" name="filtro" required>
                    <option value="" selected>Buscar por</option>
                    <option value="username">Username</option>
                    <option value="mes">Mês (MM)</option>
                    <option value="modelo">Modelo</option>
                    <option value="chassi">Chassi</option>
                    <option value="placa">Placa</option>
                    <option value="endereco">Endereço</option>
                  </select>
                  <label for="opções">Opções</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <input class="form-control me-2" id="pesquisa" name="pesquisa" type="text" required>
                </div>
              </div>

              <div class="col-md-2 align-self-center">
                <button type="submit" class="btn btn-outline-light btn-lg">Buscar</button>
              </div>
            </div>
          </form>
        </div>

        <div class="card-footer text-body-secondary">
        </div>
      </div>

    </div>
  </section>

  <section class="resultCards">

    <div class="row row-cols-1 row-cols-md-3 g-4">

      <?php
      if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
          echo '

          <div class="col">
            <div class="card h-100">
              <div class="text-center container-sm">
                <img src="' . $row['imagem'] . '" class="card-img-top rounded image-clickable" alt="...">
                <input type="hidden" value="' . $row['imagem'] . '" class="image-path">
              </div>
              <div class="card-body">
                <h5 class="card-title fw-bold">Username : ' . $row['username'] . '</h5>
                <p class="card-text">
                  <div class="d-flex flex-column m-3">
                    <div class="fs-5 fw-light">Data: ' . $row['data'] . '</div>
                    <div class="fs-5 fw-light">Modelo: ' . $row['modelo'] . '</div>
                    <div class="fs-5 fw-light">Chassi: ' . $row['chassi'] . '</div>
                    <div class="fs-5 fw-light">Placa: ' . $row['placa'] . '</div>
                    <div class="fs-5 fw-light">Endereço: ' . $row['endereco'] . '</div>
                  </div>
                </p>
                <form method="POST" action="">
                  <input type="submit" name="editar_btn" value="Editar" class="btn btn-outline-light btn-lg">
                  <input type="hidden" name="ID_servico" value="' . $row['ID_servico'] . '">
                </form>
              </div>
            </div> 
          </div>

        ';
        }
      }
      ?>

    </div>
  </section>

  <!-- Overlay para a imagem -->
  <div class="overlay" id="overlay">
    <div class="interface">
      <div class="card">
        <div class="card-header text-end">
          <button type="button" class="btn btn-outline-light  m-3" id="closeBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
            </svg>
          </button>
        </div>
        <div class="card-body text-center">
          <img src="" class="img-fluid" alt="..." id="overlayImage">
        </div>

        <div class="card-footer text-body-secondary d-flex justify-content-center">
          <div class="p-2">
            <button type="button" class="btn btn-outline-light  m-3" id="rotateBtn">
              <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
              </svg>
            </button>
          </div>
          <div class="p-2">
            <button type="button" class="btn btn-outline-light  m-3" id="flipBtn">
              <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5m14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const value = document.getElementById('pesquisa');
    if (!isNaN(value) && value.length === 1 && value > 0 && value < 13) {
      document.addEventListener('DOMContentLoaded', (event) => {


        const formatInput = (input) => {
          input.addEventListener('input', () => {
            let value = input.value;
            if (value.length === 1 && value > 0) {
              input.value = '0' + value;
            }
          });
        };

        formatInput(value);
      });
    }
  </script>

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


  <?php
  $mysqli->close();
  ?>

</body>

</html>