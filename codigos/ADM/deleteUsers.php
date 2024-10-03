<?php
include("../conexao.php");
include("../protect.php");
protect();

$mensagem = [];
$mostrarcodigo = false;

$stmt = $mysqli->prepare("SELECT * FROM `usuarios` WHERE `username` != '$_SESSION[username]' AND ID != 0"); // Preparar a consulta
if ($stmt === false) {
  die("Erro na preparação: " . $mysqli->error);
}

$stmt->execute(); //executar consulta
$result = $stmt->get_result(); //obter resultado

// função para deletar fotos
function deleteimg($username)
{
  global $mysqli, $mensagem;

  $stmt = $mysqli->prepare("SELECT `fotoDNI`, `fotoPassaporte`,`fotoA1` FROM `usuarios` WHERE `username`='$username'"); // Preparar a consulta
  if ($stmt === false) {
    die("Erro na preparação: " . $mysqli->error);
  }

  $stmt->execute(); //executar consulta
  $result = $stmt->get_result(); //obter resultado


  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

      if (file_exists($row['fotoDNI'])) {
        if (unlink($row['fotoDNI'])) {
          if (file_exists($row['fotoPassaporte'])) {
            if (unlink($row['fotoPassaporte'])) {

              if (file_exists($row['fotoA1'])) {
                if (unlink($row['fotoA1'])) {
                  return true;
                } else {
                  $mensagem[] = 'Erro ao excluir foto A1';
                  return false; // Ocorreu um erro ao excluir a foto
                }
              } else {
                return true;
              }
            } else {
              $mensagem[] = 'Erro ao excluir foto passaporte';
              return false; // Ocorreu um erro ao excluir a foto
            }
          }
        } else {
          $mensagem[] = 'Erro ao excluir foto DNI';
          return false; // Ocorreu um erro ao excluir a foto
        }
      }
    }
  }
  $stmt->close();
}

if (isset($_POST['deletar_btn'])) {
  $mostrarcodigo = true;
  $username = strtolower($_POST['username']);
}

if (isset($_POST['prosseguir_btn'])) {
  $mostrarcodigo = false;
  $username = strtolower($_POST['username']);

  if (deleteimg($username)) {
    $stmt = $mysqli->prepare("DELETE FROM `usuarios` WHERE username='$username'"); // Preparar a consulta
    if ($stmt == false) {
      die("Erro na preparação: " . $mysqli->error);
    }
    $stmt->execute(); //executar consulta
    if ($stmt->affected_rows > 0) {
      $_SESSION['sucesso'] = 1;
      header('Location: ./ADMmenu.php');
      exit();
    } else {
      $mensagem[] = "Erro ao deletar usuário.";
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
  <link rel="stylesheet" href="../css/usuarios.css">
  <link rel="stylesheet" href="../css/responsive.css" media="screen">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  <!-- /Bootstrap -->

  <!-- scripts -->
  <script src="../js/modal-icon.js"></script>
  <!-- /script -->

  <title>Usuários cadastrados</title>
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
                <a class="nav-link fw-semibold" href="../sair.php" title="Remover usuário">Sair</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>
  </header>

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

          <?php if ($mostrarcodigo) : ?>

            <div class="alert alert-light d-flex align-items-center" role="alert">
              <div class="d-flex flex-column mb-3">
                <div class="p-2">
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="p-2">
                  <div class="d-flex flex-row mb-3">
                    <div class="p-2">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                      </svg>
                    </div>
                    <div class="p-2">
                      <p>Tem certeza que deseja excluir esse usuário?</p>
                      <form method="POST" action="">
                        <input type="hidden" name="username" value="<?php echo $username; ?>">
                        <input type="submit" name="prosseguir_btn" value="Prosseguir" title="Excluir usuário" class="btn btn-outline-dark m-2">
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          <?php endif; ?>

        </div>

      </div>

      <div class="interface">
        <div class="card text-center">
          <div class="card-header">
            <div class="fs-2 fw-semibold vermelho">Usuários cadastrados</div>
          </div>
          <div class="card-footer text-body-secondary">
          </div>
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
              <div class="card-body">
                <h5 class="card-title fw-bold">Nome completo : ' . $row['nomeCompleto'] . '</h5>
                <p class="card-text">
                  <div class="d-flex flex-column m-3">
                    <div class="fs-5 fw-light">Username: ' . $row['username'] . '</div>
                    <div class="fs-5 fw-light">Email: ' . $row['email'] . '</div>
                    <div class="fs-5 fw-light">Telefone: ' . $row['telefone'] . '</div>
                    <div class="fs-5 fw-light">Função: ' . $row['funcao'] . '</div>
                    <div class="fs-5 fw-light"> 
                      <div class="d-flex">
                        <div class="p-2 w-100">DNI: ' . $row['DNI'] . '</div>
                        <input type="hidden" class="image-path" name="fotoDNI" value="' . $row['fotoDNI'] . '">
                        <div class="p-2 flex-shrink-1">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera camera-icon" viewBox="0 0 16 16">
                            <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4z" />
                            <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5m0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                          </svg>
                        </div>
                      </div>
                    </div>

                    <div class="fs-5 fw-light"> 
                      <div class="d-flex">
                        <div class="p-2 w-100">Passaporte: ' . $row['passaporte'] . '</div>
                        <div class="p-2 flex-shrink-1">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera camera-icon" viewBox="0 0 16 16">
                            <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4z" />
                            <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5m0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                          </svg>
                        </div>
                        <input type="hidden" class="image-path" name="fotoPassaporte" value="' . $row['fotoPassaporte'] . '">
                      </div>
                    </div>

                    <div class="fs-5 fw-light"> 
                      <div class="d-flex">
                        <div class="p-2 w-100">A1: ' . $row['A1'] . '</div>
                        <input type="hidden" class="image-path" name="fotoa1" value="' . $row['fotoA1'] . '">
                        <div class="p-2 flex-shrink-1">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera camera-icon" viewBox="0 0 16 16">
                            <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4z" />
                            <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5m0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                </p>
                <form method="POST" action="">
                  <input type="submit" name="deletar_btn" value="Excluir" title="Excluir usuário" class="btn btn-outline-light btn-sm">
                  <input type="hidden" name="username" value="' . $row['username'] . '">
                </form>
              </div>
            </div> 
          </div>

        ';
        }
        $stmt->close(); //fechar declaração
      } else {
        $mensagem[] = "Nenhuma solicitação de acesso pendente!";
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

  <?php $mysqli->close(); ?>

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