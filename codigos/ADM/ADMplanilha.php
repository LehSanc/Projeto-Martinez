<?php
include("../protect.php");
include("../conexao.php");
protect();

$filtro_planilha = $_SESSION['filtro_planilha'];
$filtros = explode(',', $filtro_planilha);

if ($filtros[0] == '') {
    $stmt = $mysqli->prepare("SELECT * FROM `servicos` WHERE YEAR(data) = '$filtros[1]' and MONTH(data) BETWEEN '$filtros[2]' and '$filtros[3]'"); // Preparar a consulta
} else {
    $stmt = $mysqli->prepare("SELECT * FROM `servicos` WHERE `username` = '$filtros[0]' AND YEAR(data) = '$filtros[1]' and MONTH(data) BETWEEN '$filtros[2]' and '$filtros[3]'"); // Preparar a consulta
}

if ($stmt === false) {
    die("Erro na preparação: " . $mysqli->error);
}

$stmt->execute(); //executar consulta
$result = $stmt->get_result(); //obter resultado

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/planilha.css">
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

    <title>Planilha</title>
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

            <div id="tabela">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Username</th>
                            <th scope="col">Data</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Modelo</th>
                            <th scope="col">Chassi</th>
                            <th scope="col">Placa</th>
                            <th scope="col">Endereço</th>
                            <th scope="col">Serviço</th>
                            <th scope="col">Auxiliar 1</th>
                            <th scope="col">Auxiliar 2</th>
                            <th scope="col">Auxiliar 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $qnt_linhas = mysqli_num_rows($result);
                        if ($result->num_rows > 0) {
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['data']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['chassi']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['placa']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['endereco']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['servico_prestado']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['aux1']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['aux2']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['aux3']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            $mensagem[] = "Não foi encontrada nenhuma correspondência no sistema.";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-row-reverse fixed-bottom">
                <div class="p-2">
                    <button class="btn btn-outline-light btn-sm mt-5" id="generate-excel">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-xlsx" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM7.86 14.841a1.13 1.13 0 0 0 .401.823q.195.162.479.252.284.091.665.091.507 0 .858-.158.355-.158.54-.44a1.17 1.17 0 0 0 .187-.656q0-.336-.135-.56a1 1 0 0 0-.375-.357 2 2 0 0 0-.565-.21l-.621-.144a1 1 0 0 1-.405-.176.37.37 0 0 1-.143-.299q0-.234.184-.384.188-.152.513-.152.214 0 .37.068a.6.6 0 0 1 .245.181.56.56 0 0 1 .12.258h.75a1.1 1.1 0 0 0-.199-.566 1.2 1.2 0 0 0-.5-.41 1.8 1.8 0 0 0-.78-.152q-.44 0-.777.15-.336.149-.527.421-.19.273-.19.639 0 .302.123.524t.351.367q.229.143.54.213l.618.144q.31.073.462.193a.39.39 0 0 1 .153.326.5.5 0 0 1-.085.29.56.56 0 0 1-.255.193q-.168.07-.413.07-.176 0-.32-.04a.8.8 0 0 1-.249-.115.58.58 0 0 1-.255-.384zm-3.726-2.909h.893l-1.274 2.007 1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415H1.5l1.24-2.016-1.228-1.983h.931l.832 1.438h.036zm1.923 3.325h1.697v.674H5.266v-3.999h.791zm7.636-3.325h.893l-1.274 2.007 1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415h-.861l1.24-2.016-1.228-1.983h.931l.832 1.438h.036z" />
                        </svg>
                        Exportar Excel
                    </button>
                </div>
                <div class="p-2">
                    <button class="btn btn-outline-light btn-sm mt-5" onclick="generatePDF()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                            <path d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.7 11.7 0 0 0-1.997.406 11.3 11.3 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.245.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 7.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z" />
                        </svg>
                        Exportar PDF
                    </button>
                </div>
            </div>

        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>

    <script>
        async function generatePDF() {
            const {
                jsPDF
            } = window.jspdf;

            const pdf = new jsPDF();

            const tabela = document.getElementById('tabela');
            const rows = [];
            const headers = [];

            // Pegue o cabeçalho da tabela
            const headerCells = tabela.querySelectorAll('thead tr th');
            headerCells.forEach(headerCell => {
                headers.push(headerCell.innerText);
            });

            // Pegue os dados da tabela
            const bodyRows = tabela.querySelectorAll('tbody tr');
            bodyRows.forEach(bodyRow => {
                const row = [];
                const cells = bodyRow.querySelectorAll('td');
                cells.forEach(cell => {
                    row.push(cell.innerText);
                });
                rows.push(row);
            });

            // Gera a tabela no PDF
            pdf.autoTable({
                head: [headers],
                body: rows
            });

            pdf.save('Martinez');
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#generate-excel").click(function(e) {
                e.preventDefault();

                var data = [];
                var headers = [];
                var rows = [];

                // Obter a tabela
                var table = document.getElementById('tabela');

                // Obter cabeçalhos
                var ths = table.getElementsByTagName('thead')[0].getElementsByTagName('tr')[0].getElementsByTagName('th');
                for (var i = 0; i < ths.length; i++) {
                    headers.push(ths[i].innerText);
                }
                data.push(headers);

                // Obter linhas
                var trs = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                for (var j = 0; j < trs.length; j++) {
                    var tds = trs[j].getElementsByTagName('td');
                    var row = [];
                    for (var k = 0; k < tds.length; k++) {
                        row.push(tds[k].innerText);
                    }
                    rows.push(row);
                }

                data = data.concat(rows);

                // Criar uma planilha
                var ws = XLSX.utils.aoa_to_sheet(data);

                // Criar um novo livro de trabalho
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Martinez");

                // Gerar arquivo e fazer download
                XLSX.writeFile(wb, 'Martinez.xlsx');
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>