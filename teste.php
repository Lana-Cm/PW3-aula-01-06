<?php
$host    = 'localhost';
$db      = 'katcat';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Inicializa as variáveis dos filtros para não dar erro na primeira vez que abrir a página
$statusFiltro = $_GET['status'] ?? 'ativo';
$cidadeFiltro = $_GET['cidade'] ?? ''; // Começa vazia para a pessoa digitar

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Criamos a base da query SQL
    $sql = "SELECT id, nome, email, status, cidade FROM usuario WHERE 1=1";
    $params = [];

    // Se o usuário digitou uma cidade, adicionamos o filtro dinamicamente
    if (!empty($cidadeFiltro)) {
        $sql .= " AND cidade LIKE :cidade";
        $params['cidade'] = '%' . $cidadeFiltro . '%'; // O LIKE permite buscar por partes do nome (ex: "São" acha "São Paulo")
    }

    // Adiciona o filtro de status
    $sql .= " AND status = :status";
    $params['status'] = $statusFiltro;

    // Prepara e executa de forma 100% segura com PDO
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Dinâmica - PDO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5" style="max-width: 900px;">

        <!-- Formulário de Filtros -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">🔍 Buscar Usuários</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="teste.php" class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label for="cidade" class="form-label font-weight-bold">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade"
                            placeholder="Ex: São Paulo" value="<?= htmlspecialchars($cidadeFiltro) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="ativo" <?= $statusFiltro == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inativo" <?= $statusFiltro == 'inativo' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Tabela de Resultados -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Resultados da Busca</h4>
                <span class="badge bg-light text-dark">PDO + Dynamic WHERE</span>
            </div>
            <div class="card-body">

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Erro:</strong> <?= $erro ?>
                    </div>
                <?php else: ?>

                    <?php if (!empty($resultados)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">E-mail</th>
                                        <th scope="col">Cidade</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultados as $usuario): ?>
                                        <tr>
                                            <th><?= $usuario['id'] ?></th>
                                            <td><?= htmlspecialchars($usuario['nome'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($usuario['email'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($usuario['cidade'] ?? '') ?></td>
                                            <td>
                                                <span class="badge <?= $usuario['status'] == 'ativo' ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= htmlspecialchars($usuario['status'] ?? '') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0" role="alert">
                            Nenhum usuário encontrado para os filtros aplicados.
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>
    </div>

</body>

</html>