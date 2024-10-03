<?php
include("../conexao.php");
include("../protect.php");
protect();
$mensagem = [];

function verificaUsername($username)
{
  global $mensagem, $mysqli;

  $stmt = $mysqli->prepare("SELECT * FROM `servicos` WHERE `username`='$username'"); // Preparar a consulta
  if ($stmt === false) {
    die("Erro na preparação: " . $mysqli->error);
  }

  $stmt->execute(); //executar consulta
  $result = $stmt->get_result(); //obter resultado


  if ($result->num_rows > 0) {
    return true;
  } else {
    $mensagem[] = "Usuário não registrou nenhum serviço.";
    return false;
  }
  $stmt->close(); //fechar declaração
}

function verificarMes($mes)
{
  global $mensagem;
  if (is_numeric($mes) && $mes > 0 && $mes < 13) {
    return true;
  } else {
    $mensagem[] = "Intervalo mensal inválido";
    return false;
  }
}

function verificarAno($ano)
{
  global $mensagem;
  if (is_numeric($ano) && strlen($ano) == 4) {
    return true;
  } else {
    $mensagem[] = "Ano inválido";
    return false;
  }
}

if (isset($_POST['mesInicio']) && isset($_POST['mesFim']) && isset($_POST['ano'])) {

  $mesInicio = $_POST['mesInicio'];
  $mesFim = $_POST['mesFim'];
  $ano = $_POST['ano'];
  $username = isset($_POST['username']) ? strtolower($_POST['username']) : '';

  if ($mesInicio > $mesFim) {
    $mensagem[] = "Informe um intervalo mensal válido.";
  } else {

    if (verificarAno($ano) && verificarMes($mesInicio) && verificarMes($mesFim) && ($username != '' && verificaUsername($username) || $username == '')) {
      $mesInicio = str_pad($mesInicio, 2, "0", STR_PAD_LEFT);
      $mesFim = str_pad($mesFim, 2, "0", STR_PAD_LEFT);

      $_SESSION['filtro_planilha'] = $username . ',' . $ano . ',' . $mesInicio . ',' . $mesFim;
      header("Location: ./ADMplanilha.php");
      exit();
    }
  }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/menuPlanilha.css">
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

  <title>Menu planilha</title>
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
          <div class="fs-2 fw-semibold vermelho">Menu planilha</div>
          <div class="fs-5 fw-light">Gere documentos com os serviços adicionados!</div>
        </div>
        <div class="card-body">
          <form method="POST" action="" class="needs-validation" novalidate>
            <div class="d-flex flex-column mb-3">

              <div class="p-2">
                <label for="Auxiliares" class="form-label p-inicial fs-5">Gerar planilha por username?</label><br>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gerar_planilha" id="0" value="0" required>
                  <label class="form-check-label" for="0">Não</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gerar_planilha" id="1" value="1" required>
                  <label class="form-check-label" for="1">Sim</label>
                </div>
              </div>

              <div class="p-2" id="extra-fields-container"></div>

              <div class="p-2">
                <label for="ano" class="form-label p-inicial fs-5 m-2">Ano</label>
                <input type="number" class="form-control form-control-lg mb-3" name="ano" maxlength="4" required>
              </div>

              <div class="p-2">
                <div class="input-group mb-3">
                  <label for="mês" class="form-label p-inicial fs-5 m-2">Intervalo mensal</label>
                  <input type="number" class="form-control" placeholder="Mês" aria-label="mesInicio" name="mesInicio" maxlength="2" required>
                  <span class="input-group-text">-</span>
                  <input type="number" class="form-control" placeholder="Mês" aria-label="mesFim" name="mesFim" maxlength="2" required>
                </div>
              </div>

              <div class="p-2">
                <button type="submit" class="btn btn-outline-light btn-lg m-2">Gerar</button>
              </div>
            </div>
          </form>
        </div>

        <div class="card-footer text-body-secondary">
        </div>
      </div>

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
    document.addEventListener('DOMContentLoaded', (event) => {
      const radioButtons = document.querySelectorAll('input[name="gerar_planilha"]');
      const extraFieldsContainer = document.getElementById('extra-fields-container');

      radioButtons.forEach(radio => {
        radio.addEventListener('change', () => {
          const selectedValue = parseInt(radio.value);

          // Limpar os campos extras antes de adicionar os novos
          extraFieldsContainer.innerHTML = '';

          // Adicionar os campos extras de acordo com o valor selecionado
          if (selectedValue === 1) {
            const div = document.createElement('div');
            div.classList.add('form-group');

            const label = document.createElement('label');
            label.for = `username`;
            label.classList.add('form-label', 'p-inicial', 'fs-5');
            label.innerText = `username`;

            const input = document.createElement('input');
            input.type = 'text';
            input.classList.add('form-control', 'form-control-lg', 'mb-3');
            input.id = `username`;
            input.name = `username`;
            input.placeholder = 'Username';
            input.required = true;

            div.appendChild(label);
            div.appendChild(input);
            extraFieldsContainer.appendChild(div);
          }
        });
      });
    });
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