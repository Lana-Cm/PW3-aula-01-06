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

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Filtros para a busca
    $statusFiltro = 'ativo';
    $cidadeFiltro = 'São Paulo';

    $sql = "SELECT id, nome, email FROM usuario WHERE status = :status AND cidade = :cidade";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'status' => $statusFiltro,
        'cidade' => $cidadeFiltro
    ]);

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
    <title>Consulta de Usuários - PDO</title>
    <!-- Link do Bootstrap para o visual bonito -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Lista de Usuários Filtrados</h4>
                <span class="badge bg-light text-dark">PDO + MySQL</span>
            </div>
            <div class="card-body">

                <!-- Exibe mensagem se houver erro de conexão -->
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Erro de conexão:</strong> <?= $erro ?>
                    </div>
                <?php else: ?>

                    <!-- Filtros aplicados destacados -->
                    <div class="mb-4 text-muted small">
                        <strong>Filtros ativos:</strong>
                        Status: <span class="badge bg-secondary"><?= htmlspecialchars($statusFiltro) ?></span> |
                        Cidade: <span class="badge bg-secondary"><?= htmlspecialchars($cidadeFiltro) ?></span>
                    </div>

                    <?php if (!empty($resultados)): ?>
                        <!-- Tabela estilizada -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" style="width: 80px;">ID</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">E-mail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultados as $usuario): ?>
                                        <tr>
                                            <th scope="row"><?= $usuario['id'] ?></th>
                                            <td><?= htmlspecialchars($usuario['nome'] ?? 'Não preenchido') ?></td>
                                            <td><?= htmlspecialchars($usuario['email'] ?? 'Não preenchido') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <!-- Alerta caso não ache ninguém -->
                        <div class="alert alert-warning mb-0" role="alert">
                            Nenhum usuário encontrado para os filtros selecionados.
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>
    </div>

</body>

</html>