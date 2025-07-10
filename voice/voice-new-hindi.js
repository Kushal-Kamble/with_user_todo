// 🟢 Preload voice for Chrome
document.addEventListener("click", () => {
  window.speechSynthesis.cancel();
  const preload = new SpeechSynthesisUtterance("Voice assistant activated");
  preload.lang = "en-IN";
  window.speechSynthesis.speak(preload);
}, { once: true });

// 🧠 Detect Hindi
function isHindi(text) {
  const hindiPattern = /[ऀ-ॿ]/;
  return hindiPattern.test(text);
}

// 🎤 Voice button
document.getElementById("voiceBtn").addEventListener("click", () => {
  if (!('webkitSpeechRecognition' in window)) {
    alert("🎤 Voice recognition is not supported in your browser.");
    return;
  }

  const recognition = new webkitSpeechRecognition();
  recognition.lang = "hi-IN"; // Set default to Hindi. Will auto-detect from text.
  recognition.interimResults = false;

  recognition.onresult = function (event) {
    const command = event.results[0][0].transcript.toLowerCase();
    const isHindiLang = isHindi(command);
    console.log("🔊 Command:", command, " | Hindi:", isHindiLang);

    const userId = $("input[name=user_id]").val();

    fetch("php/tasks.php?action=get&user_id=" + userId)
      .then(res => res.json())
      .then(tasks => {
        const pendingTasks = tasks.filter(t => t.completed == 0);
        const completedTasks = tasks.filter(t => t.completed == 1);
        const modalBody = document.querySelector("#taskModalVoice .modal-body");
        modalBody.innerHTML = "";

        let msg = "", speakText = "";

        // ✅ PENDING TASKS
        if (command.includes("pending") || command.includes("incomplete") || command.includes("बाकी")) {
          if (pendingTasks.length > 0) {
            speakText += isHindiLang
              ? `आपके पास ${pendingTasks.length} लंबित कार्य हैं।\n`
              : `You have ${pendingTasks.length} pending tasks.\n`;

            pendingTasks.forEach((t, i) => {
              modalBody.innerHTML += `
                <div class="mb-3 border-bottom pb-2">
                  <h5 class="text-dark mb-1">${t.title}</h5>
                  <p class="text-muted small mb-1">${t.description}</p>
                  <p class="small mb-0"><strong>📅 Deadline:</strong> ${t.deadline}</p>
                </div>`;
              speakText += isHindiLang
                ? `कार्य ${i + 1}: ${t.title}, अंतिम तिथि ${t.deadline}। `
                : `Task ${i + 1}: ${t.title}, deadline is ${t.deadline}. `;
            });

            msg = isHindiLang
              ? `📋 आपके पास ${pendingTasks.length} लंबित कार्य हैं।`
              : `📋 You have ${pendingTasks.length} pending tasks.`;

            new bootstrap.Modal(document.getElementById("taskModalVoice")).show();
          } else {
            msg = isHindiLang ? "✅ कोई लंबित कार्य नहीं है।" : "✅ No pending tasks.";
            speakText = isHindiLang
              ? "आपने सभी कार्य पूरे कर लिए हैं।"
              : "All your tasks are completed.";
          }
        }

        // ✅ COMPLETED TASKS
        else if (command.includes("complete") || command.includes("done") || command.includes("पूर्ण")) {
          if (completedTasks.length > 0) {
            speakText += isHindiLang
              ? `आपने ${completedTasks.length} कार्य पूरे किए हैं। `
              : `You have completed ${completedTasks.length} tasks. `;

            completedTasks.forEach((t, i) => {
              modalBody.innerHTML += `
                <div class="mb-3 border-bottom pb-2">
                  <h5 class="text-success mb-1">${t.title}</h5>
                  <p class="text-muted small mb-1">${t.description}</p>
                  <p class="small mb-0"><strong>📅 Deadline:</strong> ${t.deadline}</p>
                </div>`;
              speakText += isHindiLang
                ? `कार्य ${i + 1}: ${t.title}, अंतिम तिथि ${t.deadline}। `
                : `Task ${i + 1}: ${t.title}, deadline was ${t.deadline}. `;
            });

            msg = isHindiLang
              ? `✅ आपने ${completedTasks.length} कार्य पूरे किए हैं।`
              : `✅ You have completed ${completedTasks.length} tasks.`;

            new bootstrap.Modal(document.getElementById("taskModalVoice")).show();
          } else {
            msg = isHindiLang ? "😓 कोई कार्य पूरा नहीं किया गया।" : "😓 No tasks completed yet.";
            speakText = isHindiLang
              ? "आपने अभी तक कोई कार्य पूरा नहीं किया है।"
              : "You haven't completed any tasks yet.";
          }
        }

        // ❌ Unknown command
        else {
          msg = isHindiLang
            ? "⚠️ माफ़ कीजिए, मैं आपका आदेश नहीं समझ पाया।"
            : "⚠️ Sorry, I didn’t understand that command.";
          speakText = msg;
        }

        showToast(msg);
        speakAI(speakText, isHindiLang);
      })
      .catch(err => {
        console.error("❌ Fetch error:", err);
        const failMsg = "Server error. Please try again later.";
        showToast(failMsg);
        speakAI(failMsg, false);
      });
  };

  recognition.onerror = function (e) {
    const errMsg = "🎙️ Voice recognition error: " + e.error;
    console.error(errMsg);
    showToast(errMsg);
    speakAI("There was an error with voice recognition. Please try again.", false);
  };

  recognition.start();
});

// 🔊 Speak using Indian voice
function speakAI(text, isHindiLang = false) {
  window.speechSynthesis.cancel();
  const utterance = new SpeechSynthesisUtterance(text);
  const voices = window.speechSynthesis.getVoices();

  const preferredVoices = isHindiLang
    ? ["Microsoft Hemant - Hindi (India)", "Google हिन्दी"]
    : ["Google UK English Male", "Google Indian English Male", "Google en-IN"];

  const matchedVoice = voices.find(voice =>
    preferredVoices.some(name => voice.name.includes(name))
  );

  if (matchedVoice) {
    utterance.voice = matchedVoice;
  } else {
    utterance.lang = isHindiLang ? "hi-IN" : "en-IN";
  }

  utterance.pitch = 1;
  utterance.rate = 1;
  utterance.volume = 1;

  setTimeout(() => {
    window.speechSynthesis.speak(utterance);
  }, 100);
}

// 🔔 Toast
function showToast(message) {
  const toastBox = document.getElementById("toastMessage");
  toastBox.innerText = message;
  const toast = new bootstrap.Toast(document.getElementById("toastVoice"));
  toast.show();
}

// 🧠 Preload voices
window.speechSynthesis.onvoiceschanged = () => {
  window.speechSynthesis.getVoices();
};
