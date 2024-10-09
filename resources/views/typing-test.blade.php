<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #282c34;
            color: #ffffff;
            font-family: Arial, sans-serif;
            margin-top: 50px;
        }
        #textToType {
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 2px solid #ffffff;
            display: inline-block;
            padding: 10px;
        }
        #testArea {
            border: 2px solid #ffffff;
            padding: 15px;
            min-height: 100px;
            background-color: #333;
            color: #ffffff;
            font-size: 20px;
            margin-bottom: 20px;
        }
        #testArea:focus {
            outline: none;
            border-color: #61dafb; /* Warna biru cerah saat fokus */
        }
        .result {
            display: none;
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 5px;
        }
        .correct {
            color: #28a745; /* hijau */
        }
        .incorrect {
            color: #dc3545; /* merah */
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1>Typing Test</h1>
        <p id="textToType">{{ $randomText }}</p>
        <div id="testArea" contenteditable="true" placeholder="Start typing..."></div>
        <input type="hidden" id="originalText" value="{{ $randomText }}">

        <div class="result">
            <h3>Results:</h3>
            <p id="resultWPM"></p>
            <p id="resultAccuracy"></p>
            <p id="resultCorrectChars"></p>
            <p id="resultIncorrectChars"></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let startTime;

        $('#testArea').on('focus', function() {
            if (!startTime) {
                startTime = new Date();
            }
        });

        $('#testArea').on('keypress', function(event) {
            if (event.which === 13) { // Enter key
                event.preventDefault(); // Mencegah baris baru
                submitResult();
            }
        });

        function submitResult() {
            const typedText = $('#testArea').text().trim();
            const originalText = $('#originalText').val();
            const timeTaken = (new Date() - startTime) / 1000; // waktu dalam detik

            $.post("{{ route('calculate.result') }}", {
                typedText: typedText,
                originalText: originalText,
                timeTaken: timeTaken,
                _token: "{{ csrf_token() }}"
            }, function(data) {
                $('#resultWPM').text('WPM: ' + data.wpm);
                $('#resultAccuracy').text('Accuracy: ' + data.accuracy + '%');
                $('#resultCorrectChars').text('Correct Characters: ' + data.correctChars);
                $('#resultIncorrectChars').text('Incorrect Characters: ' + data.incorrectChars);
                $('.result').show();

                // Ambil teks acak baru dan bersihkan area mengetik
                getRandomText();
                $('#testArea').text(''); // Clear the test area
                $('#testArea').attr("contenteditable", true); // Enable editing again
                startTime = null; // Reset start time for the next test
            });
        }

        function getRandomText() {
            $.get("{{ route('typingTest.randomText') }}", function(data) {
                $('#textToType').text(data.randomText);
                $('#originalText').val(data.randomText);
            });
        }
    </script>
</body>
</html>
