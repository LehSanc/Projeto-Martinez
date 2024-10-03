<?php
include("conexao.php");
if (!isset($_SESSION)) session_start();
$mensagem = [];

// Duas pessoas não podem ter o mesmo passaporte e nem DNI
// Portanto, caso já exista uma solicitação com esses documentos ela não poderá ser realizada novamente a menos que o ADM recuse o acesso
function verificaSolicitacao($DNI, $passaporte)
{
    global $mensagem, $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM `usuarios` WHERE `DNI`='$DNI' AND `passaporte`='$passaporte'"); // Preparar a consulta
    if ($stmt === false) {
        die("Erro na preparação: " . $mysqli->error);
    }

    $stmt->execute(); //executar consulta
    $result = $stmt->get_result(); //obter resultado

    if ($result->num_rows > 0) {
        $mensagem[] = "Já existe uma solicitação de cadastro com esses documentos, por favor aguarde o e-mail de confirmação.";
        return false;
    } else {
        return true;
    }
    $stmt->close(); //fechar declaração
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
        $mensagem[] = "O username escolhido já está em uso, por favor escolha um diferente.";
        return false;
    } else {
        return true;
    }
    $stmt->close(); //fechar declaração
}

function uploadimg($file, $documentName)
{
    $targetDir = "docs/";
    $fileName = basename($file["name"]);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Verifica a extensão do arquivo
    if (!in_array($fileExtension, ['png', 'jpg', 'jpeg'])) {
        return false; // Retorna false para indicar erro
    }

    // Define o nome e caminho do arquivo
    $targetFile = $targetDir . $documentName . '.' . $fileExtension;

    // Move o arquivo para o diretório de destino
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile; // Retorna o caminho completo do arquivo
    } else {
        return false; // Retorna false para indicar erro
    }
}

function verificaSenha($senha1, $senha2)
{
    global $mensagem;
    if ($senha1 == $senha2) {
        return password_hash($senha1, PASSWORD_DEFAULT);
    } else {
        $mensagem[] = "Por favor confirme a mesma senha.";
        return false;
    }
}

if (isset($_POST['nome']) && isset($_POST['sobrenome']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['telefone']) && isset($_POST['funcao']) && isset($_POST['DNI']) && isset($_FILES["fotoDNI"]) && isset($_POST['passaporte']) && isset($_FILES["fotoPassaporte"]) && isset($_POST['senha1']) && isset($_POST['senha2'])) {

    $DNI = strtolower($_POST['DNI']);
    $passaporte = strtolower($_POST['passaporte']);

    if (verificaSolicitacao($DNI, $passaporte)) {

        $nomeCompleto = strtolower($_POST['nome']) . ' ' . strtolower($_POST['sobrenome']);
        $username = verificaUsername(strtolower($_POST['username'])) ? strtolower($_POST['username']) : false;
        $email = verificaEmail($_POST['email']) ? $_POST['email'] : false;
        $telefone = $_POST['telefone'];
        $funcao = strtolower($_POST['funcao']);
        $senha1 = $_POST['senha1'];
        $senha2 = $_POST['senha2'];

        $fotoDNI = uploadimg($_FILES['fotoDNI'], $DNI);
        $fotoPassaporte = uploadimg($_FILES['fotoPassaporte'], $passaporte);

        if (!empty($_POST['A1']) && isset($_FILES["fotoA1"]) && $_FILES["fotoA1"]["error"] == UPLOAD_ERR_OK) {
            $A1 = strtolower($_POST['A1']);
            $fotoA1 = uploadimg($_FILES['fotoA1'], $A1);
        } else {
            $A1 = '';
            $fotoA1 = '';
        }

        $senha = verificaSenha($senha1, $senha2);

        if ($fotoPassaporte && $fotoDNI && ($fotoA1 || $fotoA1 === '') && $senha && $email) {

            $stmt = $mysqli->prepare("INSERT INTO `usuarios`(`userName`, `nomeCompleto`, `email`, `telefone`, `funcao`, `DNI`, `passaporte`, `A1`, `fotoDNI`, `fotoPassaporte`, `fotoA1`, `senha`) VALUES ('$username','$nomeCompleto','$email','$telefone','$funcao','$DNI','$passaporte','$A1','$fotoDNI','$fotoPassaporte','$fotoA1','$senha')");
            if ($stmt == false) {
                die("Erro na preparação: " . $mysqli->error);
            }
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $sucesso = true;
                $mensagem[] = "Sua solicitação de cadastro foi recebida! Caso aceita, um e-mail será enviado e você poderá fazer login.";
            } else {
                $sucesso = true;
                $mensagem[] = "Erro ao solicitar cadastro.";
            }
            $stmt->close();
        }
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

    <title>Solicitar cadastro</title>
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

        </div>
        <div class="overlay"></div>
    </section>

    <section class="formulario pt-4">
        <div class="interface">

            <div class="d-flex flex-column mb-3 text-center">
                <div class="pt-2 fs-1 fw-bold">Novo por aqui?</div>
                <div class="fs-3 fw-light vermelho">Solicite um cadastro!</div>
                <div class="p-2 fs-5 fw-light">Após o preenchimento do formulário você poderá entrar mediante autorização do adiministrador.</div>
            </div>

            <form method="POST" action="" class="needs-validation row g-3" enctype="multipart/form-data" novalidate>

                <div class="col-md-4">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>

                <div class="col-md-4">
                    <label for="sobrenome" class="form-label">Sobrenome</label>
                    <input type="text" class="form-control" id="sobrenome" name="sobrenome" required>
                </div>

                <div class="col-md-4">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group" id="username">
                        <span class="input-group-text" id="username">@</span>
                        <input type="text" class="form-control" id="username" name="username" aria-describedby="username" required>
                    </div>
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
                        <input type="text" class="form-control" id="email" name="email" aria-describedby="email" required>
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
                        <input type="number" class="form-control" id="telefone" name="telefone" aria-describedby="telefone" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="funcao" class="form-label">Função</label>
                    <div class="input-group" id="funcao">
                        <span class="input-group-text" id="funcao">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tools" viewBox="0 0 16 16">
                                <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3q0-.405-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708M3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z" />
                            </svg>
                        </span>
                        <input type="text" class="form-control" id="funcao" name="funcao" aria-describedby="função" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="DNI" class="form-label">DNI</label>
                    <input type="text" class="form-control" id="DNI" name="DNI" required>
                </div>
                <div class="col-md-4">
                    <label for="passaporte" class="form-label">Passaporte</label>
                    <input type="text" class="form-control" id="passaporte" name="passaporte" required>
                </div>
                <div class="col-md-4">
                    <label for="A1" class="form-label">A1</label>
                    <input type="text" class="form-control" id="A1" name="A1">
                </div>

                <div class="col-md-4">
                    <div class="input-group">
                        <label for="fotoDNI" class="input-group-text">DNI</label>
                        <input type="file" class="form-control" id="fotoDNI" name="fotoDNI" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group">
                        <label for="fotoPassaporte" class="input-group-text">Passaporte</label>
                        <input type="file" class="form-control" id="fotoPassaporte" name="fotoPassaporte" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group">
                        <label for="fotoA1" class="input-group-text">A1</label>
                        <input type="file" class="form-control" id="fotoA1" name="fotoA1">
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="senha1" class="form-label">Por favor, defina uma senha:</label>
                    <div class="input-group">
                        <span class="input-group-text" id="senha1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                                <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56" />
                                <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415" />
                            </svg>
                        </span>
                        <input type="password" class="form-control" id="senha1" aria-describedby="senha" name="senha1" required>
                    </div>
                </div>


                <div class="col-md-4">
                    <label for="senha2" class="form-label">Confirme a senha:</label>
                    <div class="input-group">
                        <span class="input-group-text" id="senha2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                                <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56" />
                                <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415" />
                            </svg>
                        </span>
                        <input type="password" class="form-control" id="senha2" aria-describedby="senha" name="senha2" required>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-outline-dark btn-lg my-3">Enviar solicitação de cadastro</button>
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