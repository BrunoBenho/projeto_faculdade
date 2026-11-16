<?php
// Conexão com o MySQL
$conexao = mysqli_connect("localhost", "root", "", "processostrabalhistas");

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}
mysqli_set_charset($conexao, "utf8mb4");

$mensagem = "";
$tipo = "ok";
$operacao = "incluir";          // variável de controle
$registro = "";
$advogado = $trabalhador = $empresa = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $operacao = $_POST["operacao"] ?? "incluir";
    $registro = $_POST["registro"] ?? "";

    if ($operacao === "excluir") {
        $stmt = mysqli_prepare($conexao, "DELETE FROM processos WHERE registro = ?");
        mysqli_stmt_bind_param($stmt, "i", $registro);
        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
            $mensagem = "Processo $registro excluído com sucesso!";
        } else {
            $mensagem = "Erro ao excluir o processo.";
            $tipo = "erro";
        }
        mysqli_stmt_close($stmt);
        $operacao = "incluir";
        $registro = "";
    } else {
        $advogado    = trim($_POST["advogado"] ?? "");
        $trabalhador = trim($_POST["trabalhador"] ?? "");
        $empresa     = trim($_POST["empresa"] ?? "");

        if ($advogado === "" || $trabalhador === "" || $empresa === "") {
            $mensagem = "Preencha todos os campos obrigatórios.";
            $tipo = "erro";
        } elseif ($operacao === "alterar") {
            $stmt = mysqli_prepare($conexao, "UPDATE processos SET advogado = ?, trabalhador = ?, empresa = ? WHERE registro = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $advogado, $trabalhador, $empresa, $registro);
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = "Processo $registro alterado com sucesso!";
                $operacao = "incluir";
                $registro = $advogado = $trabalhador = $empresa = "";
            } else {
                $mensagem = "Erro ao alterar o processo.";
                $tipo = "erro";
            }
            mysqli_stmt_close($stmt);
        } else {
            $stmt = mysqli_prepare($conexao, "INSERT INTO processos (advogado, trabalhador, empresa) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $advogado, $trabalhador, $empresa);
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = "Processo incluído com sucesso! Registro nº " . mysqli_insert_id($conexao);
                $advogado = $trabalhador = $empresa = "";
            } else {
                $mensagem = "Erro ao incluir o processo.";
                $tipo = "erro";
            }
            mysqli_stmt_close($stmt);
        }
    }
} elseif (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $stmt = mysqli_prepare($conexao, "SELECT * FROM processos WHERE registro = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($linha = mysqli_fetch_assoc($res)) {
        $operacao    = "alterar";
        $registro    = $linha["registro"];
        $advogado    = $linha["advogado"];
        $trabalhador = $linha["trabalhador"];
        $empresa     = $linha["empresa"];
    } else {
        $mensagem = "Processo não encontrado.";
        $tipo = "erro";
    }
    mysqli_stmt_close($stmt);
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }

$lista = mysqli_query($conexao, "SELECT * FROM processos");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Controle de Processos Trabalhistas</title>
<style>
    body { font-family: Arial, sans-serif; background: #eef1f5; margin: 0; padding: 20px; color: #333; }
    .container { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.15); }
    h1 { text-align: center; color: #1f3a5f; margin-top: 0; }
    h2 { color: #1f3a5f; border-bottom: 2px solid #1f3a5f; padding-bottom: 5px; }
    label { display: block; margin-top: 12px; font-weight: bold; }
    input[type=text] { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #aaa; border-radius: 4px; box-sizing: border-box; }
    button, .btn { background: #1f3a5f; color: #fff; border: 0; padding: 9px 18px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; display: inline-block; }
    button:hover, .btn:hover { opacity: .85; }
    .cancelar { background: #777; }
    .excluir { background: #c0392b; padding: 5px 12px; }
    .editar { background: #2980b9; padding: 5px 12px; }
    .msg { padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    .ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .erro { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    th { background: #1f3a5f; color: #fff; }
    tr:nth-child(even) { background: #f5f7fa; }
    form.inline { display: inline; }
    .botoes { margin-top: 15px; }
</style>
</head>
<body>
<div class="container">
    <h1>Controle de Processos Trabalhistas</h1>

    <?php if ($mensagem !== ""): ?>
        <div class="msg <?= $tipo ?>"><?= h($mensagem) ?></div>
    <?php endif; ?>

    <h2><?= $operacao === "alterar" ? "Alterar processo nº " . h($registro) : "Novo processo" ?></h2>
    <form method="post" action="processos.php">
        <input type="hidden" name="registro" value="<?= h($registro) ?>">
        <input type="hidden" name="operacao" value="<?= h($operacao) ?>">

        <label for="advogado">Advogado</label>
        <input type="text" id="advogado" name="advogado" maxlength="100" required value="<?= h($advogado) ?>">

        <label for="trabalhador">Trabalhador</label>
        <input type="text" id="trabalhador" name="trabalhador" maxlength="100" required value="<?= h($trabalhador) ?>">

        <label for="empresa">Empresa</label>
        <input type="text" id="empresa" name="empresa" maxlength="100" required value="<?= h($empresa) ?>">

        <div class="botoes">
            <button type="submit"><?= $operacao === "alterar" ? "Alterar" : "Incluir" ?></button>
            <?php if ($operacao === "alterar"): ?>
                <a class="btn cancelar" href="processos.php">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>

    <h2>Processos cadastrados</h2>
    <table>
        <tr><th>Registro</th><th>Advogado</th><th>Trabalhador</th><th>Empresa</th><th>Ações</th></tr>
        <?php while ($p = mysqli_fetch_assoc($lista)): ?>
        <tr>
            <td><?= h($p["registro"]) ?></td>
            <td><?= h($p["advogado"]) ?></td>
            <td><?= h($p["trabalhador"]) ?></td>
            <td><?= h($p["empresa"]) ?></td>
            <td>
                <a class="btn editar" href="processos.php?editar=<?= (int)$p["registro"] ?>">Editar</a>
                <form class="inline" method="post" action="processos.php"
                      onsubmit="return confirm('Deseja realmente excluir o processo <?= (int)$p["registro"] ?>?');">
                    <input type="hidden" name="operacao" value="excluir">
                    <input type="hidden" name="registro" value="<?= (int)$p["registro"] ?>">
                    <button type="submit" class="excluir">Excluir</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
<?php mysqli_close($conexao); ?>
