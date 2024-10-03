<?php
include("../protect.php");
include("../conexao.php");
protect();

$mensagem = [];

$ID_servico = $_SESSION['ID_servico'];

// Obter os dados do serviço para pré preencher o formulário
$stmt = $mysqli->prepare("SELECT * FROM `servicos` WHERE `ID_servico` = '$ID_servico'");

if ($stmt === false) {
  die("Erro na preparação: " . $mysqli->error);
}

$stmt->execute(); //executar consulta
$result = $stmt->get_result(); //obter resultado

if ($result->num_rows > 0) {
  $servico = $result->fetch_assoc();
} else {
  $mensagem[] = "Desculpe, não foi possível recuperar os dados.";
}
$stmt->close();

function deleteimg()
{
  global $mysqli, $mensagem, $ID_servico;

  $stmt = $mysqli->prepare("SELECT `imagem` FROM `servicos` WHERE `ID_servico` = '$ID_servico'"); // Preparar a consulta
  if ($stmt === false) {
    die("Erro na preparação: " . $mysqli->error);
  }

  $stmt->execute(); //executar consulta
  $result = $stmt->get_result(); //obter resultado


  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

      if (file_exists($row['imagem'])) {
        if (unlink($row['imagem'])) {
          return true;
        } else {
          $mensagem[] = 'Erro ao excluir imagem anterior';
          return false; // Ocorreu um erro ao excluir a foto
        }
      }
    }
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
    $mensagem[] = "Usuário não cadastrado no sistema. Verifique os valores dos campos auxiliares.";
    return false;
  }
  $stmt->close(); //fechar declaração
}

function verificarDATA($dia, $mes, $ano)
{
  global $mensagem;
  if ((is_numeric($dia) && $dia < 32 && $dia > 0) &&
    (is_numeric($mes) && $mes < 13 && $mes > 0) &&
    (is_numeric($ano) && strlen($ano) == 4)
  ) {
    return true;
  } else {
    $mensagem[] = "Data inválida.";
    return false;
  }
}

function uploadimg($placa)
{
  global $mensagem;
  $microtime = microtime(true);
  $hora = date('Y-m-d H:i:s', $microtime);
  $targetDir = "../uploads/";
  $fotoName = basename($_FILES["foto"]["name"]);
  $imageFileType = strtolower(pathinfo($fotoName, PATHINFO_EXTENSION));
  $newFileName = $placa . $hora . "." . $imageFileType; // Novo nome da imagem com base na placa
  $fotoPath = $targetDir . $newFileName;

  $check = getimagesize($_FILES["foto"]["tmp_name"]);
  if ($check === false) {
    $mensagem[] = "O arquivo não é uma imagem válida.";
    return false;
  }

  if (move_uploaded_file($_FILES["foto"]["tmp_name"], $fotoPath)) {
    return $fotoPath; // Retorna o caminho onde a imagem será salva
  } else {
    $mensagem[] = "Erro ao fazer upload da foto.";
    return false;
  }
}

if (isset($_POST['alterar_btn'])) {
  global $mysqli, $mensagem;
  if (isset($_POST['dia']) && isset($_POST['mes']) && isset($_POST['ano']) && isset($_POST['marca']) && isset($_POST['modelo']) && isset($_POST['chassi']) && isset($_POST['placa']) && isset($_POST['endereco']) && isset($_POST['servico_prestado'])) {

    $marca = strtolower($_POST['marca']);
    $modelo = strtolower($_POST['modelo']);
    $chassi = strtolower($_POST['chassi']);
    $placa = strtolower($_POST['placa']);
    $endereco = strtolower($_POST['endereco']);
    $servico_prestado = strtolower($_POST['servico_prestado']);

    $aux1 = strtolower($_POST['aux1']) ?? '';
    $aux2 = strtolower($_POST['aux2']) ?? '';
    $aux3 = strtolower($_POST['aux3']) ?? '';

    if (verificarDATA($_POST['dia'], $_POST['mes'], $_POST['ano'])) {

      if (($aux1 != '' && verificaUsername($aux1) || $aux1 == '') && ($aux2 != '' && verificaUsername($aux2) || $aux2 == '') && ($aux3 != '' && verificaUsername($aux3) || $aux3 == '')) {

        $data = $_POST['ano'] . '-' . $_POST['mes'] . '-' . $_POST['dia'];

        if (isset($_FILES["foto"]) && deleteimg()) {
          $fotoPath = uploadimg($placa);
          if ($fotoPath != false) {
            $stmt = $mysqli->prepare("UPDATE `servicos` SET `imagem`='$fotoPath',`data`='$data',`marca`='$marca',`modelo`='$modelo',`chassi`='$chassi',`placa`='$placa',`endereco`='$endereco',`servico_prestado`='$servico_prestado',`aux1`='$aux1',`aux2`='$aux2',`aux3`='$aux3' WHERE `ID_servico`= '$ID_servico'");
          }
        } else {
          $stmt = $mysqli->prepare("UPDATE `servicos` SET `data`='$data',`marca`='$marca',`modelo`='$modelo',`chassi`='$chassi',`placa`='$placa',`endereco`='$endereco',`servico_prestado`='$servico_prestado',`aux1`='$aux1',`aux2`='$aux2',`aux3`='$aux3' WHERE `ID_servico`= '$ID_servico'");
        }

        if ($stmt === false) {
          die("Erro na preparação: " . $mysqli->error);
        }

        $stmt->execute(); //executar consulta
        if ($stmt->affected_rows > 0) {
          $_SESSION['sucesso'] = 1;
          header('Location: ./menuEditar.php');
          exit();
        } else {
          $mensagem[] = "Erro ao alterar serviço.";
        }
        $stmt->close();
      }
    }
  }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/formServicos.css">
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

  <title>Editar serviço</title>
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
                <a class="nav-link" aria-current="page" href="./meuPerfil.php" title="Meu perfil">Meu perfil</a>
              </li>

              <h4 class="fw-bold">Ações</h4>
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="./menu.php" title="Menu">Menu</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="./adicionar.php" title="Adicionar serviço">Adicionar</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="./menuEditar.php" title="Editar serviço">Editar</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="./menuPlanilha.php" title="Gerar planilha">Planilha</a>
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

      <div class="card text-center">
        <div class="card-header">
          <div class="fs-2 fw-semibold vermelho">Editar serviço</div>
        </div>
        <div class="card-body">
          <form method="POST" action="" class="needs-validation" enctype="multipart/form-data" novalidate>
            <div class="d-flex flex-column mb-3">

              <div class="p-2">
                <div class="row gy-5">
                  <div class="col-12">
                    <img src="<?php echo $servico['imagem']; ?>" class="img-thumbnail" alt="...">
                  </div>
                </div>
              </div>

              <div class="p-2">
                <label for="imagem" class="form-label p-inicial fs-5">Alterar imagem?</label><br>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="nova_imagem" id="0" value="0" required>
                  <label class="form-check-label" for="0">Não</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="nova_imagem" id="1" value="1" required>
                  <label class="form-check-label" for="1">Sim</label>
                </div>
              </div>

              <div class="p-2" id="extra-fields-container"></div>


              <div class="p-2">
                <div class="input-group mb-3">
                  <input type="number" class="form-control" placeholder="Dia" aria-label="dia" name="dia" maxlength="2" value="<?php echo date('d', strtotime($servico['data'])); ?>" required>
                  <span class="input-group-text">/</span>
                  <input type="number" class="form-control" placeholder="Mês" aria-label="mes" name="mes" maxlength="2" value="<?php echo date('m', strtotime($servico['data'])); ?>" required>
                  <span class="input-group-text">/</span>
                  <input type="number" class="form-control" placeholder="Ano" aria-label="ano" name="ano" maxlength="4" value="<?php echo date('Y', strtotime($servico['data'])); ?>" required>
                </div>
              </div>

              <div class="p-2">
                <label for="marca" class="form-label p-inicial fs-5">Marca</label>
                <div class="input-group" id="marca">
                  <span class="input-group-text" id="marca">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                      <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="marca" name="marca" value="<?php echo $servico['marca']; ?>" required>
                </div>
              </div>

              <div class="p-2">
                <label for="modelo" class="form-label p-inicial fs-5">Modelo</label>
                <div class="input-group" id="modelo">
                  <span class="input-group-text" id="modelo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                      <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="modelo" name="modelo" value="<?php echo $servico['modelo']; ?>" required>
                </div>
              </div>

              <div class="p-2">
                <label for="chassi" class="form-label p-inicial fs-5">Chassi</label>
                <div class="input-group" id="chassi">
                  <span class="input-group-text" id="chassi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                      <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="chassi" name="chassi" value="<?php echo $servico['chassi']; ?>" required>
                </div>
              </div>

              <div class="p-2">
                <label for="placa" class="form-label p-inicial fs-5">Placa</label>
                <div class="input-group" id="placa">
                  <span class="input-group-text" id="placa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                      <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="placa" name="placa" value="<?php echo $servico['placa']; ?>" required>
                </div>
              </div>

              <div class="p-2">
                <label for="endereco" class="form-label p-inicial fs-5">Endereço</label>
                <div class="input-group" id="endereco">
                  <span class="input-group-text" id="endereco">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z" />
                      <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo $servico['endereco']; ?>" required>
                </div>
              </div>

              <div class="p-2">
                <label for="servico_prestado" class="form-label p-inicial fs-5">Serviço prestado</label>
                <div class="input-group" id="servico_prestado">
                  <span class="input-group-text" id="servico_prestado">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hammer" viewBox="0 0 16 16">
                      <path d="M9.972 2.508a.5.5 0 0 0-.16-.556l-.178-.129a5 5 0 0 0-2.076-.783C6.215.862 4.504 1.229 2.84 3.133H1.786a.5.5 0 0 0-.354.147L.146 4.567a.5.5 0 0 0 0 .706l2.571 2.579a.5.5 0 0 0 .708 0l1.286-1.29a.5.5 0 0 0 .146-.353V5.57l8.387 8.873A.5.5 0 0 0 14 14.5l1.5-1.5a.5.5 0 0 0 .017-.689l-9.129-8.63c.747-.456 1.772-.839 3.112-.839a.5.5 0 0 0 .472-.334" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="servico_prestado" name="servico_prestado" value="<?php echo $servico['servico_prestado']; ?>" required>
                </div>
              </div>

              <div class="p-2">

              </div>

              <div class="p-2">
                <label for="aux1" class="form-label p-inicial fs-5">Auxiliar</label>
                <div class="input-group" id="aux1">
                  <span class="input-group-text" id="aux1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                      <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="aux1" name="aux1" value="<?php echo $servico['aux1']; ?>">
                </div>
              </div>

              <div class="p-2">
                <label for="aux2" class="form-label p-inicial fs-5">Auxiliar</label>
                <div class="input-group" id="aux2">
                  <span class="input-group-text" id="aux2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                      <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="aux2" name="aux2" value="<?php echo $servico['aux2']; ?>">
                </div>
              </div>

              <div class="p-2">
                <label for="aux3" class="form-label p-inicial fs-5">Auxiliar</label>
                <div class="input-group" id="aux3">
                  <span class="input-group-text" id="aux3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                      <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                    </svg>
                  </span>
                  <input type="text" class="form-control" id="aux3" name="aux3" value="<?php echo $servico['aux3']; ?>">
                </div>
              </div>

              <div class="p-2">
                <button type="submit" class="btn btn-outline-light btn-lg" name="alterar_btn">Salvar</button>
              </div>

            </div>
          </form>
        </div>

        <div class="card-footer text-body-secondary">
        </div>
      </div>

    </div>

  </section>

  <!-- script verificar data -->
  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const diaInput = document.getElementById('dia');
      const mesInput = document.getElementById('mes');

      const formatInput = (input) => {
        input.addEventListener('input', () => {
          let value = input.value;
          if (value.length === 1 && value > 0) {
            input.value = '0' + value;
          }
        });
      };

      formatInput(diaInput);
      formatInput(mesInput);
    });
  </script>


  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const radioButtons = document.querySelectorAll('input[name="nova_imagem"]');
      const extraFieldsContainer = document.getElementById('extra-fields-container');

      radioButtons.forEach(radio => {
        radio.addEventListener('change', () => {
          const selectedValue = parseInt(radio.value);

          // Limpar os campos extras antes de adicionar os novos
          extraFieldsContainer.innerHTML = '';

          if (selectedValue === 1) {
            const div = document.createElement('div');
            div.classList.add('form-group');

            const label = document.createElement('label');
            label.htmlFor = `foto`;
            label.classList.add('form-label', 'p-inicial', 'fs-5');
            label.innerText = `Foto`;

            const divInputGroup = document.createElement('div');
            divInputGroup.className = 'input-group';

            const span = document.createElement('span');
            span.className = 'input-group-text';

            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
            svg.setAttribute('width', '16');
            svg.setAttribute('height', '16');
            svg.setAttribute('fill', 'currentColor');
            svg.className = 'bi bi-paperclip';
            svg.setAttribute('viewBox', '0 0 16 16');

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', 'M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z');
            svg.appendChild(path);

            span.appendChild(svg);

            const input = document.createElement('input');
            input.type = 'file';
            input.classList.add('form-control');
            input.id = `foto`;
            input.name = `foto`;
            input.placeholder = 'foto';
            input.accept = '.jpg, .jpeg, .png';
            input.required = true;

            divInputGroup.appendChild(span);
            divInputGroup.appendChild(input);
            div.appendChild(label);
            div.appendChild(divInputGroup);
            extraFieldsContainer.appendChild(div);
          }
        });
      });
    });
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

</body>

</html>