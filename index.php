<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PSME Account Deletion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6fb;
    }
    .header-img {
      height: 360px;
      object-fit: cover;
      border-bottom-left-radius: 60px;
      border-bottom-right-radius: 60px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .form-container {
      margin-top: -100px;
      background: white;
      border-radius: 20px;
      padding: 2.5rem;
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
      border: 1px solid #e0e0e0;
      animation: fadeInUp 0.8s ease-out;
    }
    .form-wrapper {
      max-width: 460px;
      width: 100%;
    }
    .form-label {
      font-weight: 600;
      color: #344767;
    }
    .form-control:focus {
      border-color: #0b1e63;
      box-shadow: 0 0 0 0.2rem rgba(11, 30, 99, 0.25);
    }
    .btn-primary {
      background-color: #0b1e63;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0a1851;
    }
    .toast-container {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1055;
      animation: popIn 0.6s ease-out;
    }
    #status-box.pending {
      background-color: #fff8e5;
      border-color: #ffc107;
    }
    #status-box.success {
      background-color: #e6ffed;
      border-color: #28a745;
    }
    #status-box.failed {
      background-color: #ffe5e5;
      border-color: #dc3545;
    }
    .countdown {
      font-size: 0.9rem;
      color: #6c757d;
      margin-top: 10px;
    }
    .form-title {
      color: #0b1e63;
    }
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes popIn {
      0% { opacity: 0; transform: scale(0.9) translate(-50%, -50%); }
      100% { opacity: 1; transform: scale(1) translate(-50%, -50%); }
    }
  </style>
  <!-- ✅ Include reCAPTCHA script in <head> -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-white">

  <div class="w-100 overflow-hidden">
    <img src="img/Header.png" alt="Header" class="w-100 header-img" />
  </div>

  <div class="container d-flex justify-content-center">
    <div class="form-container form-wrapper text-center">
      <div class="mb-3">
        <img src="img/psmeinc.png" alt="PSME Logo" width="60" height="60">
      </div>
      <h4 class="fw-bold mb-4">PSME ACCOUNT DELETION</h4>

      <!-- Step 1 -->
      <form id="step1-form" onsubmit="checkStatus(event)" class="text-start">
        <div class="mb-3">
          <label for="email" class="form-label fw-medium">Email</label>
          <input type="email" id="email" name="email" class="form-control rounded-3" placeholder="Enter your email" required>
        </div>
        <button type="submit" class="btn w-100 text-white" style="background-color:#0b1e63;">REQUEST FOR DELETION</button>

        <div class="text-center mt-2">
          <a href="https://psmeinc.org.ph/#/psme/terms-and-conditions" target="_blank" class="text-decoration-none" style="font-size: 0.9rem; color: #6c757d;">
            <i class="bi bi-shield-lock"></i> Privacy Policy
          </a>
        </div>
      </form>

      <!-- Step 2 -->
      <form id="step2-form" class="text-start d-none" onsubmit="openConfirmModal(event)">
        <div class="mb-3">
          <label class="form-label fw-medium">Name</label>
          <input type="text" name="name" id="name" class="form-control rounded-3" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-medium">Email</label>
          <input type="email" name="confirmed_email" id="confirmed_email" class="form-control rounded-3 bg-light" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label fw-medium">Birthday</label>
          <input type="text" name="birthday" id="birthday" placeholder="MM/DD/YYYY" class="form-control rounded-3" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-medium">PRC License Number</label>
          <input type="text" name="prc_number" id="prc_number" class="form-control rounded-3" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-medium">Reason for Deletion</label>
          <textarea name="reason" id="reason" class="form-control rounded-3" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn w-100 text-white" style="background-color:#0b1e63;">
          SUBMIT
        </button>
      </form>

      <!-- Status Section -->
      <div id="status-section" class="d-none text-center mt-4">
        <p class="mb-1">Status for: <strong id="status-email"></strong></p>
        <div id="status-box" class="p-4 mt-3 bg-light border-start border-5 border-warning-subtle rounded shadow-sm">
          <div id="status-message" class="h5 mb-2"></div>
          <div id="status-icon" class="fs-1"></div>
          <div id="countdown" class="countdown"></div>
        </div>
        <button id="cancel-btn" class="btn btn-danger mt-3 d-none" data-bs-toggle="modal" data-bs-target="#cancelModal">
          Cancel Deletion Request
        </button>
        <button class="btn btn-outline-secondary mt-3" onclick="window.location.reload()">Back</button>
      </div>
    </div>
  </div>

  <!-- Confirm Delete Modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content border-0 rounded-4">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="confirmModalLabel">Confirm Account Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="confirm-details" class="mb-3"></div>
          <!-- ✅ Google reCAPTCHA -->
          <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="6LfnFpQrAAAAANhfFIbHFhXbWCQ0pslkAckNkFNx"></div>
          </div>
          <div class="text-end">
            <button type="button" class="btn btn-danger px-4 fw-medium" onclick="submitDeletion()">Submit</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Cancel Confirmation Modal -->
  <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cancel Deletion Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to cancel your account deletion request?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger" onclick="cancelDeletion()">Yes, Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Toast placeholder -->
  <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function checkStatus(event) {
      event.preventDefault();
      const email = document.getElementById("email").value;

      fetch('check_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'new') {
          document.getElementById('step1-form').classList.add('d-none');
          document.getElementById('step2-form').classList.remove('d-none');
          document.getElementById('confirmed_email').value = email;
        } else if (data.status === 'existing') {
          document.getElementById('step1-form').classList.add('d-none');
          document.getElementById('status-section').classList.remove('d-none');
          document.getElementById('status-email').textContent = data.email;

          const statusMessage = document.getElementById('status-message');
          const statusIcon = document.getElementById('status-icon');
          const countdown = document.getElementById('countdown');
          const cancelBtn = document.getElementById('cancel-btn');

          if (data.account_status === 'Successful') {
            statusMessage.textContent = 'Successfully Deleted';
            statusMessage.className = 'h5 mb-2 text-success';
            statusIcon.innerHTML = '<span class="text-success">&#10003;</span>';
            countdown.textContent = '';
            cancelBtn.classList.add("d-none");
          } else if (data.account_status === 'Pending') {
            statusMessage.textContent = 'Pending';
            statusMessage.className = 'h5 mb-2 text-warning';
            statusIcon.innerHTML = '<span class="text-warning">Pending...</span>';
            cancelBtn.classList.remove("d-none");

            if (data.created_at) {
              const requestDate = new Date(data.created_at);
              const expirationDate = new Date(requestDate);
              expirationDate.setDate(expirationDate.getDate() + 30);

              function updateCountdown() {
                const now = new Date();
                const remainingTime = expirationDate - now;
                if (remainingTime > 0) {
                  const days = Math.floor(remainingTime / (1000 * 60 * 60 * 24));
                  const hours = Math.floor((remainingTime / (1000 * 60 * 60)) % 24);
                  const minutes = Math.floor((remainingTime / (1000 * 60)) % 60);
                  const seconds = Math.floor((remainingTime / 1000) % 60);
                  countdown.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s remaining until deletion.`;
                } else {
                  countdown.textContent = 'Deletion window expired.';
                  clearInterval(timerInterval);
                }
              }
              updateCountdown();
              const timerInterval = setInterval(updateCountdown, 1000);
            }
          } else {
            statusMessage.textContent = data.account_status;
            statusMessage.className = 'h5 mb-2 text-danger';
            statusIcon.innerHTML = '';
            countdown.textContent = '';
            cancelBtn.classList.add("d-none");
          }
        } else {
          showToast("System error or invalid email.", "danger");
        }
      });
    }

    function showToast(message, type = "success") {
      const toast = document.createElement("div");
      toast.className = `toast align-items-center text-bg-${type} border-0 show mb-2`;
      toast.setAttribute("role", "alert");
      toast.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      `;
      document.getElementById("toast-container").appendChild(toast);
      setTimeout(() => toast.remove(), 5000);
    }

    function openConfirmModal(event) {
      event.preventDefault();
      // Get values from the form
      const name = document.getElementById('name').value;
      const email = document.getElementById('confirmed_email').value;
      const birthday = document.getElementById('birthday').value;
      const prc = document.getElementById('prc_number').value;
      const reason = document.getElementById('reason').value;

      // Show details in modal (not editable)
      document.getElementById('confirm-details').innerHTML = `
        <div><strong>Name:</strong> ${name}</div>
        <div><strong>Email:</strong> ${email}</div>
        <div><strong>Birthday:</strong> ${birthday}</div>
        <div><strong>PRC License Number:</strong> ${prc}</div>
        <div><strong>Reason:</strong> ${reason}</div>
      `;

      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
      modal.show();
    }

    function submitDeletion() {
      const recaptchaResponse = grecaptcha.getResponse();
      if (!recaptchaResponse) {
        showToast("⚠️ Please complete the CAPTCHA", "danger");
        return;
      }

      // Gather details from the form (Step 2)
      const name = document.getElementById('name').value;
      const email = document.getElementById('confirmed_email').value;
      const birthday = document.getElementById('birthday').value;
      const prc = document.getElementById('prc_number').value;
      const reason = document.getElementById('reason').value;

      const formData = new FormData();
      formData.append('name', name);
      formData.append('confirmed_email', email);
      formData.append('birthday', birthday);
      formData.append('prc_number', prc);
      formData.append('reason', reason);
      formData.append('g-recaptcha-response', recaptchaResponse);

      fetch("submit_deletion.php", {
        method: "POST",
        body: formData
      })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
          document.querySelector('.modal-backdrop')?.remove();
          document.getElementById("step2-form").classList.add("d-none");
          showToast("✅ Your account deletion request has been submitted.", "success");
        } else {
          showToast(data.error || "Something went wrong.", "danger");
        }
      })
      .catch(() => showToast("⚠️ Failed to submit. Please try again.", "danger"));
    }

    function cancelDeletion() {
      const email = document.getElementById('status-email').textContent;
      fetch('cancel_deletion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('cancelModal'));
          modal.hide();
          showToast("✅ Your deletion request has been cancelled.", "success");
          setTimeout(() => window.location.reload(), 3000);
        } else {
          showToast("⚠️ Failed to cancel deletion. Try again.", "danger");
        }
      });
    }
  </script>
</body>
</html>