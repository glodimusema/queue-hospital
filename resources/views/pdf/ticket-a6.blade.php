<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
            font-size: 12px;
        }

        .title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .ticket {
            font-size: 42px;
            font-weight: bold;
            margin: 12px 0;
        }

        .service {
            font-size: 16px;
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="title">HÔPITAL CHARITÉ MATERNELLE</div>

    <div class="line"></div>

    <div>Votre numéro</div>

    <div class="ticket">
        {{ $ticket->numero_ticket }}
    </div>

    <div class="service">
        {{ $ticket->service->nom_service }}
    </div>

    <p>
        Veuillez attendre votre appel à l’écran.
    </p>

    <div class="line"></div>

    <div class="small">
        Date : {{ now()->format('d/m/Y H:i') }}<br>
        Cabinet : {{ $ticket->cabinet->nom_cabinet ?? 'Non affecté' }}
    </div>
</body>
</html>