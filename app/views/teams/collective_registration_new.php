<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscrizione Collettiva</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Iscrizione Collettiva</h1>
        <p>Team: <?= htmlspecialchars($team['nome'] ?? 'N/A') ?></p>
        
        <form action="/teams/collective-registration/<?= $team['id'] ?? 1 ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="event_id">Evento</label>
                <select name="event_id" id="event_id" class="form-control" required>
                    <option value="">Seleziona evento</option>
                    <?php if (isset($available_events)): ?>
                        <?php foreach ($available_events as $event): ?>
                            <option value="<?= $event['id'] ?>">
                                <?= htmlspecialchars($event['titolo'] ?? 'N/A') ?> - €<?= number_format($event['prezzo_base'] ?? 0, 2) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="responsabile_nome">Nome Responsabile</label>
                <input type="text" name="responsabile_nome" id="responsabile_nome" class="form-control" 
                       value="<?= htmlspecialchars($team['referente_nome'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="responsabile_email">Email Responsabile</label>
                <input type="email" name="responsabile_email" id="responsabile_email" class="form-control" 
                       value="<?= htmlspecialchars($team['referente_email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="responsabile_telefono">Telefono Responsabile</label>
                <input type="tel" name="responsabile_telefono" id="responsabile_telefono" class="form-control" 
                       value="<?= htmlspecialchars($team['referente_telefono'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="excel_file">File Excel/CSV</label>
                <input type="file" name="excel_file" id="excel_file" class="form-control" 
                       accept=".xlsx,.xls,.csv" required>
            </div>

            <div class="form-group">
                <label for="note">Note (opzionale)</label>
                <textarea name="note" id="note" class="form-control" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Procedi con Iscrizione Collettiva</button>
        </form>

        <p><a href="/teams/view/<?= $team['id'] ?? 1 ?>" class="btn btn-secondary">Torna al Team</a></p>
    </div>
</body>
</html>
