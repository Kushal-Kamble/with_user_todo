document.getElementById("voiceBtn").addEventListener("click", () => {
  if (!('webkitSpeechRecognition' in window)) {
    alert("🎤 Voice recognition is not supported in your browser.");
    return;
  }

  const recognition = new webkitSpeechRecognition();
  recognition.lang = "hi-IN"; // Supports Hindi/English both
  recognition.interimResults = false;

  recognition.onstart = function () {
    console.log("🎤 Voice listening started...");
  };

  recognition.onresult = function (event) {
    const command = event.results[0][0].transcript.toLowerCase();
    console.log("🔊 You said:", command);

    const userId = $("input[name=user_id]").val();

    fetch("php/tasks.php?action=get&user_id=" + userId)
      .then(res => res.json())
      .then(tasks => {
        let msg = "";
        let speakText = "";

        if (
          command.includes("pending") ||
          command.includes("incomplete") ||
          command.includes("अधूरा") ||
          command.includes("लंबित")
        ) {
          const pendingTasks = tasks.filter(t => t.completed == 0);
          if (pendingTasks.length > 0) {
            const first = pendingTasks[0];

            msg = `🔔 Incomplete Task: ${first.title}\n📅 Deadline: ${first.deadline}`;

            // Show AI modal with details
            document.getElementById("modalTaskTitle").innerText = first.title;
            document.getElementById("modalTaskDesc").innerText = first.description;
            document.getElementById("modalTaskDeadline").innerText = first.deadline;
            const myModal = new bootstrap.Modal(document.getElementById("taskModalVoice"));
            myModal.show();

            // Voice output (Hindi + English)
            speakText = `आपका अधूरा कार्य '${first.title}' है। इसकी डेडलाइन है ${first.deadline}. Your incomplete task is '${first.title}' and its deadline is ${first.deadline}.`;
          } else {
            msg = "✅ कोई लंबित कार्य नहीं है।";
            speakText = "आपके सभी कार्य पूरे हो चुके हैं। You have no pending tasks.";
          }
        } else {
          msg = "⚠️ कमांड समझ में नहीं आई।";
          speakText = "Sorry, I didn’t understand. कृपया दोबारा कहें।";
        }

        // Show on screen
        showToast(msg);
        speakAI(speakText);
      })
      .catch(err => {
        console.error("❌ Task fetch error:", err);
        const failMsg = "टास्क लोड करने में समस्या हुई।";
        showToast(failMsg);
        speakAI("टास्क सर्वर से प्राप्त नहीं हो सके। कृपया बाद में प्रयास करें।");
      });
  };

  recognition.onerror = function (e) {
    const errMsg = "🎙️ वॉइस रिकग्निशन में त्रुटि: " + e.error;
    console.error(errMsg);
    showToast(errMsg);
    speakAI("वॉइस रिकग्निशन में त्रुटि आई है। कृपया दोबारा प्रयास करें।");
  };

  recognition.start();
});

// 🎤 Voice Speaking Function (Hindi + English)
function speakAI(text) {
  const utterance = new SpeechSynthesisUtterance(text);
  utterance.lang = "en-IN"; // Indian English (can handle Hindi)
  utterance.pitch = 1;
  utterance.rate = 1;
  utterance.volume = 1;
  window.speechSynthesis.speak(utterance);
}

// ✅ Toast Display Function
function showToast(message) {
  const toastBox = document.getElementById("toastMessage");
  toastBox.innerText = message;
  const toast = new bootstrap.Toast(document.getElementById("toastVoice"));
  toast.show();
}
