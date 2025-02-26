let testData = [];
let selectedQuestions = [];
let correctAnswers = {};
let currentQuestionIndex = 0;
let timerInterval;

function startTest() {
    let selectedCategory = document.getElementById("categorySelect").value;
    loadPDF(selectedCategory);
}

function loadPDF(pdfUrl) {
    fetch(pdfUrl)
        .then(response => response.text())
        .then(data => {
            testData = parsePDF(data);
            selectedQuestions = testData.sort(() => Math.random() - 0.5).slice(0, 50);
            selectedQuestions.forEach((q, index) => {
                correctAnswers[`q${index}`] = q.correct;
            });
            currentQuestionIndex = 0;
            showQuestion();
            document.querySelector(".container").style.display = "none";
            document.getElementById("testContainer").style.display = "block";
        })
        .catch(error => console.error("PDF yuklanmadi", error));
}

function parsePDF(data) {
    let questions = [];
    let lines = data.split("\n");
    for (let i = 0; i < lines.length; i++) {
        if (lines[i].includes("?")) {
            let answers = [lines[i + 1], lines[i + 2], lines[i + 3], lines[i + 4]];
            let correct = answers.find(a => a.includes("*")).replace("*", "").trim();
            questions.push({ question: lines[i], answers: answers.map(a => a.replace("*", "").trim()), correct });
            i += 4;
        }
    }
    return questions;
}

function showQuestion() {
    let questionContainer = document.getElementById("questions");
    let questionData = selectedQuestions[currentQuestionIndex];

    let shuffledAnswers = questionData.answers.sort(() => Math.random() - 0.5);
    questionContainer.innerHTML = `<p class="question">${currentQuestionIndex + 1}. ${questionData.question}</p>`;

    shuffledAnswers.forEach(answer => {
        let id = `q${currentQuestionIndex}_${answer}`;
        questionContainer.innerHTML += `<input type="radio" name="q${currentQuestionIndex}" value="${answer}" id="${id}" onclick="checkAnswer('${answer}')"> 
                                         <label for="${id}">${answer}</label><br>`;
    });

    startTimer();
}

function startTimer() {
    let timeLeft = 60;
    let timerElement = document.getElementById("timer");
    timerElement.innerText = `Vaqt: ${timeLeft} soniya`;

    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        timeLeft--;
        timerElement.innerText = `Vaqt: ${timeLeft} soniya`;

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            nextQuestion();
        }
    }, 1000);
}

function checkAnswer(selectedAnswer) {
    let correct = correctAnswers[`q${currentQuestionIndex}`];
    let message = selectedAnswer === correct ? "✅ To'g'ri!" : `❌ Xato! To‘g‘ri javob: ${correct}`;
    alert(message);
}

function nextQuestion() {
    if (currentQuestionIndex < selectedQuestions.length - 1) {
        currentQuestionIndex++;
        showQuestion();
    } else {
        submitTest();
    }
}

function submitTest() {
    clearInterval(timerInterval);
    let totalQuestions = selectedQuestions.length;
    let correctCount = 0;
    let resultDiv = document.getElementById("result");

    selectedQuestions.forEach((q, index) => {
        let selectedOption = document.querySelector(`input[name="q${index}"]:checked`);
        if (selectedOption && selectedOption.value === correctAnswers[`q${index}`]) {
            correctCount++;
        }
    });

    let totalScore = (correctCount / totalQuestions) * 100;
    resultDiv.innerHTML = `<p>Sizning natijangiz: ${totalScore.toFixed(2)}%</p>`;

    document.getElementById("testContainer").style.display = "none";
    document.getElementById("resultContainer").style.display = "block";
}
