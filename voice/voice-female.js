// 🟢 Unlock voice output once (for Chrome)
document.addEventListener("click", () => {
  window.speechSynthesis.cancel();
  const preload = new SpeechSynthesisUtterance("Voice assistant activated");
  preload.lang = "en-IN"; // Indian English
  window.speechSynthesis.speak(preload);
}, { once: true });

// 🎤 Voice Button Click
document.getElementById("voiceBtn").addEventListener("click", () => {
  if (!('webkitSpeechRecognition' in window)) {
    alert("🎤 Voice recognition is not supported in your browser.");
    return;
  }

  const recognition = new webkitSpeechRecognition();
  recognition.lang = "en-US";
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
        let msg = "", speakText = "";

        if (
          command.includes("pending") ||
          command.includes("incomplete") ||
          command.includes("task status")
        ) {
          const pendingTasks = tasks.filter(t => t.completed == 0);
          if (pendingTasks.length > 0) {
            const first = pendingTasks[0];
            msg = `📝 Incomplete Task: ${first.title}\n📅 Deadline: ${first.deadline}`;

            // Show modal
            document.getElementById("modalTaskTitle").innerText = first.title;
            document.getElementById("modalTaskDesc").innerText = first.description;
            document.getElementById("modalTaskDeadline").innerText = first.deadline;
            const myModal = new bootstrap.Modal(document.getElementById("taskModalVoice"));
            myModal.show();

            speakText = `Your incomplete task is '${first.title}'. Deadline is ${first.deadline}.`;
          } else {
            msg = "✅ You have no pending tasks.";
            speakText = "All your tasks are completed.";
          }
        } else {
          msg = "⚠️ Sorry, I didn’t understand that command.";
          speakText = "Sorry, I didn’t understand your command. Please try again.";
        }

        showToast(msg);
        speakAI(speakText);
      })
      .catch(err => {
        console.error("❌ Fetch error:", err);
        const failMsg = "Server error. Please try again later.";
        showToast(failMsg);
        speakAI("Server error. Please try again later.");
      });
  };

  recognition.onerror = function (e) {
    const errMsg = "🎙️ Voice recognition error: " + e.error;
    console.error(errMsg);
    showToast(errMsg);
    speakAI("There was an error with voice recognition. Please try again.");
  };

  recognition.start();
});

// 🔊 Speak with Indian male voice preference
function speakAI(text) {
  window.speechSynthesis.cancel(); // Stop previous voice
  const utterance = new SpeechSynthesisUtterance(text);
  const voices = window.speechSynthesis.getVoices();

  // Prefer Indian English male voice
  const preferredVoices = [
    "Google हिन्दी", "Google UK English Male", "Google Indian English Male", "Microsoft Hemant"
  ];

  const indianVoice = voices.find(v =>
    preferredVoices.some(name => v.name.includes(name)) || v.lang.includes("en-IN")
  );

  if (indianVoice) {
    utterance.voice = indianVoice;
  } else {
    utterance.lang = "en-IN"; // fallback
  }

  utterance.pitch = 1;
  utterance.rate = 1;
  utterance.volume = 1;

  setTimeout(() => {
    window.speechSynthesis.speak(utterance);
  }, 100);
}

// 🔔 Toast notification
function showToast(message) {
  const toastBox = document.getElementById("toastMessage");
  toastBox.innerText = message;
  const toast = new bootstrap.Toast(document.getElementById("toastVoice"));
  toast.show();
}

// 🔁 Ensure voices preload in Chrome
window.speechSynthesis.onvoiceschanged = () => {
  window.speechSynthesis.getVoices();
};
