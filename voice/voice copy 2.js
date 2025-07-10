// 🟢 Voice preload for Chrome
document.addEventListener("click", () => {
  window.speechSynthesis.cancel();
  const preload = new SpeechSynthesisUtterance("Voice assistant activated");
  preload.lang = "en-IN";
  window.speechSynthesis.speak(preload);
}, { once: true });

document.getElementById("voiceBtn").addEventListener("click", () => {
  if (!('webkitSpeechRecognition' in window)) {
    alert("🎤 Voice recognition is not supported in your browser.");
    return;
  }

  const recognition = new webkitSpeechRecognition();
  recognition.lang = "en-US";
  recognition.interimResults = false;

  recognition.onresult = function (event) {
    const command = event.results[0][0].transcript.toLowerCase();
    console.log("🔊 You said:", command);

    const userId = $("input[name=user_id]").val();

    fetch("php/tasks.php?action=get&user_id=" + userId)
      .then(res => res.json())
      .then(tasks => {
        const pendingTasks = tasks.filter(t => t.completed == 0);
        const completedTasks = tasks.filter(t => t.completed == 1);
        const modalBody = document.querySelector("#taskModalVoice .modal-body");
        modalBody.innerHTML = "";

        if (command.includes("pending")) {
          if (pendingTasks.length > 0) {
            let speakText = `You have ${pendingTasks.length} pending tasks. `;
            pendingTasks.forEach((t, i) => {
              speakText += `Task ${i + 1}: ${t.title}, deadline is ${t.deadline}. `;
            });
            speakAI(speakText);
            showModalTasks(pendingTasks, "pending");
          } else {
            showToast("✅ You have no pending tasks.");
            speakAI("All your tasks are completed.");
          }
        }

        else if (command.includes("completed") || command.includes("done")) {
          if (completedTasks.length > 0) {
            let speakText = `You have completed ${completedTasks.length} tasks. `;
            completedTasks.forEach((t, i) => {
              speakText += `Task ${i + 1}: ${t.title}, completed on or before ${t.deadline}. `;
            });
            speakAI(speakText);
            showModalTasks(completedTasks, "completed");
          } else {
            showToast("😓 You haven't completed any tasks yet.");
            speakAI("You have not completed any tasks yet.");
          }
        }

        else {
          showToast("⚠️ Sorry, I didn’t understand that command.");
          speakAI("Sorry, I didn’t understand your command. Please try again.");
        }
      })
      .catch(err => {
        console.error("❌ Fetch error:", err);
        showToast("Server error. Please try again later.");
        speakAI("Server error. Please try again later.");
      });
  };

  recognition.onerror = function (e) {
    console.error("🎙️ Voice recognition error: " + e.error);
    showToast("🎙️ Voice recognition error: " + e.error);
    speakAI("There was an error with voice recognition. Please try again.");
  };

  recognition.start();
});

// 🔊 Speak using Indian voice
function speakAI(text) {
  window.speechSynthesis.cancel();
  const utterance = new SpeechSynthesisUtterance(text);
  const voices = window.speechSynthesis.getVoices();

  const preferredVoices = [
    "Google हिन्दी",
    "Google UK English Male",
    "Microsoft Hemant - Hindi (India)",
    "Google Indian English Male",
    "Google en-IN"
  ];

  const indianMaleVoice = voices.find(voice =>
    preferredVoices.some(name => voice.name.includes(name))
  );

  if (indianMaleVoice) {
    utterance.voice = indianMaleVoice;
  } else {
    const fallback = voices.find(v => v.lang === "en-IN");
    if (fallback) utterance.voice = fallback;
    else utterance.lang = "en-IN";
  }

  utterance.pitch = 1;
  utterance.rate = 1;
  utterance.volume = 1;

  setTimeout(() => {
    window.speechSynthesis.speak(utterance);
  }, 100);
}

// 📋 Show tasks in modal
function showModalTasks(tasks, type) {
  const modalBody = document.querySelector("#taskModalVoice .modal-body");
  modalBody.innerHTML = "";
  tasks.forEach(t => {
    modalBody.innerHTML += `
      <div class="mb-3 border-bottom pb-2">
        <h5 class="text-${type === 'completed' ? 'success' : 'dark'} mb-1">${t.title}</h5>
        <p class="text-muted small mb-1">${t.description}</p>
        <p class="small mb-0"><strong>📅 Deadline:</strong> ${t.deadline}</p>
      </div>`;
  });
  new bootstrap.Modal(document.getElementById("taskModalVoice")).show();
}

// 🔔 Toastify Message
function showToast(message) {
  const toastBox = document.getElementById("toastMessage");
  toastBox.innerText = message;
  const toast = new bootstrap.Toast(document.getElementById("toastVoice"));
  toast.show();
}

// 🧠 Preload Voices
window.speechSynthesis.onvoiceschanged = () => {
  window.speechSynthesis.getVoices();
};
