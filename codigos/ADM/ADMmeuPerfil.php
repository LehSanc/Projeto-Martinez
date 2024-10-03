<?php
include("../protect.php");
include("../conexao.php");
protect();

$mensagem = [];

$username = $_SESSION['username'];

// Obter os dados do serviço para pré preencher o formulário
$stmt = $mysqli->prepare("SELECT * FROM `usuarios` WHERE `username` = '$username'");

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

function verificaEmail($email)
{
    global $mensagem;
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        $mensagem[] = "Email inválido.";
        return false;
    }
}

if (isset($_POST['alterar_btn'])) {
    global $mysqli, $mensagem;

    if (isset($_POST['nomeCompleto'])  && isset($_POST['email']) && isset($_POST['telefone']) && isset($_POST['funcao'])) {

        $nomeCompleto = strtolower($_POST['nomeCompleto']);
        $email = strtolower($_POST['email']);
        $telefone = strtolower($_POST['telefone']);
        $funcao = strtolower($_POST['funcao']);

        if (verificaEmail($email)) {
            $stmt = $mysqli->prepare("UPDATE `usuarios` SET `nomeCompleto`='$nomeCompleto',`email`='$email',`telefone`='$telefone',`funcao`='$funcao' WHERE `username`= '$username'");


            if ($stmt === false) {
                die("Erro na preparação: " . $mysqli->error);
            }

            $stmt->execute(); //executar consulta
            if ($stmt->affected_rows > 0) {
                $_SESSION['sucesso'] = 1;
                header('Location: ./ADMmenu.php');
                exit();
            } else {
                $mensagem[] = "Erro ao realizar alteração.";
            }
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/usuarios.css">
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
    <script src="../js/modal-icon.js"></script>
    <!-- /script -->

    <title>Meu perfil</title>
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
                    <div class="fs-2 fw-semibold vermelho">Meus dados</div>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="needs-validation" enctype="multipart/form-data" novalidate>
                        <div class="row g-3">

                            <div class="col-md-7">
                                <label for="nomeCompleto" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nomeCompleto" name="nomeCompleto" value="<?php echo $servico['nomeCompleto']; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group" id="email">
                                    <span class="input-group-text" id="email">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-at" viewBox="0 0 16 16">
                                            <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z" />
                                            <path d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648m-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z" />
                                        </svg>
                                    </span>
                                    <input type="text" class="form-control" id="email" name="email" aria-describedby="email" value="<?php echo $servico['email']; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <div class="input-group" id="telefone">
                                    <span class="input-group-text" id="telefone">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-plus" viewBox="0 0 16 16">
                                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                                            <path fill-rule="evenodd" d="M12.5 1a.5.5 0 0 1 .5.5V3h1.5a.5.5 0 0 1 0 1H13v1.5a.5.5 0 0 1-1 0V4h-1.5a.5.5 0 0 1 0-1H12V1.5a.5.5 0 0 1 .5-.5" />
                                        </svg>
                                    </span>
                                    <span class="input-group-text" id="telefone">+</span>
                                    <input type="number" class="form-control" id="telefone" name="telefone" aria-describedby="telefone" value="<?php echo $servico['telefone']; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="funcao" class="form-label">Função</label>
                                <div class="input-group" id="funcao">
                                    <span class="input-group-text" id="funcao">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-square" viewBox="0 0 16 16">
                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1v-1c0-1-1-4-6-4s-6 3-6 4v1a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                                        </svg>
                                    </span>
                                    <input type="text" class="form-control" id="funcao" name="funcao" aria-describedby="função" value="<?php echo $servico['funcao']; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header"> DNI </div>
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="p-2 w-100"><?php echo $servico['DNI']; ?></div>
                                            <div class="p-2 flex-shrink-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera camera-icon" viewBox="0 0 16 16">
                                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4z" />
                                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5m0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="hidden" class="image-path" name="fotoDNI" value="<?php echo $servico['fotoDNI']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header"> Passaporte </div>
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="p-2 w-100"><?php echo $servico['passaporte']; ?></div>
                                            <div class="p-2 flex-shrink-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera camera-icon" viewBox="0 0 16 16">
                                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4z" />
                                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5m0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="hidden" class="image-path" name="fotoPassaporte" value="<?php echo $servico['fotoPassaporte']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header"> A1 </div>
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="p-2 w-100"><?php echo $servico['A1']; ?></div>
                                            <div class="p-2 flex-shrink-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera camera-icon" viewBox="0 0 16 16">
                                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4z" />
                                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5m0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="hidden" class="image-path" name="fotoA1" value="<?php echo $servico['A1']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <input type="submit" name="alterar_btn" value="Salvar alterações" class="btn btn-outline-light btn-lg">
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

    <?php
    $stmt->close();
    $mysqli->close();
    ?>

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