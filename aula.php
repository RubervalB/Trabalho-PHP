<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador Financeiro</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        #transaction-form {
            max-width: 400px;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #transaction-form label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        #transaction-form input, #transaction-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #transaction-form input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }

        #summary {
            max-width: 400px;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<?php
$servername = "localhost:3306";
$username = "root";
$password = "ruber555";
$database = "gerenciamentotarefas";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = isset($_POST["type"]) ? $_POST["type"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";
    $amount = isset($_POST["amount"]) ? $_POST["amount"] : 0;

    if ($type && $description && $amount) {
        $sql = "INSERT INTO transacoes (tipo, descricao, valor) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $type, $description, $amount);

        if ($stmt->execute()) {
            echo "<div class='success'>Transação adicionada com sucesso.</div>";
        } else {
            echo "<div class='error'>Erro ao adicionar transação: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='error'>Todos os campos são obrigatórios.</div>";
    }
}

$sql = "SELECT tipo, descricao, valor, criado_em FROM transacoes";
$result = $conn->query($sql);
?>

<div id="transaction-form">
    <label for="type">Tipo:</label>
    <select id="type" required>
        <option value="receita">Receita</option>
        <option value="despesa">Despesa</option>
    </select>

    <label for="description">Descrição:</label>
    <input type="text" id="description" required>

    <label for="amount">Valor:</label>
    <input type="number" id="amount" step="0.01" required>

    <input type="submit" value="Adicionar Transação" onclick="addTransaction()">
</div>

<div id="summary"></div>

<script>
    var transactions = [];

    function addTransaction() {
        var type = document.getElementById('type').value;
        var description = document.getElementById('description').value;
        var amount = parseFloat(document.getElementById('amount').value);

        transactions.push({ type: type, description: description, amount: amount });
        updateSummary();
    }

    function updateSummary() {
        var income = 0;
        var expenses = 0;

        transactions.forEach(function (transaction) {
            if (transaction.type === 'receita') {
                income += transaction.amount;
            } else {
                expenses += transaction.amount;
            }
        });

        var balance = income - expenses;

        var summaryElement = document.getElementById('summary');
        summaryElement.innerHTML = '<h2>Resumo Financeiro</h2>';
        summaryElement.innerHTML += '<p>Receitas: ' + income.toFixed(2) + '</p>';
        summaryElement.innerHTML += '<p>Despesas: ' + expenses.toFixed(2) + '</p>';
        summaryElement.innerHTML += '<p>Saldo: ' + balance.toFixed(2) + '</p>';
    }
</script>

</body>
</html>

<?php
$conn->close();
?>
