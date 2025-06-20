document.addEventListener('DOMContentLoaded', function() {
    // Get data from sessionStorage
    const selectedSeats = JSON.parse(sessionStorage.getItem('selectedSeats') || '[]');
    const totalAmount = sessionStorage.getItem('totalAmount') || '0';
    const journeyInfo = JSON.parse(sessionStorage.getItem('journeyInfo') || '{}');

    // Update journey info in summary panel
    updateJourneyInfo(journeyInfo);

    // Update summary panel with selected seats and total
    updateSummaryPanel(selectedSeats, totalAmount);

    // Generate passenger forms based on selected seats
    generatePassengerForms(selectedSeats);

    // Handle form submission
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', handleFormSubmit);
    }

    // Handle back button
    const backButton = document.querySelector('.btn-outline');
    if (backButton) {
        backButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'pilihkursi.html';
        });
    }
});

function updateJourneyInfo(journeyInfo) {
    const journeyInfoContainer = document.querySelector('.journey-info');
    if (!journeyInfoContainer) return;

    journeyInfoContainer.innerHTML = `
        <div>
            <i class="fas fa-bus"></i>
            <span>${journeyInfo.busName || 'Bus Name'}</span>
        </div>
        <div>
            <i class="fas fa-calendar"></i>
            <span>${journeyInfo.date || 'Date'}</span>
        </div>
        <div>
            <i class="fas fa-clock"></i>
            <span>${journeyInfo.time || 'Time'}</span>
        </div>
        <div>
            <i class="fas fa-map-marker-alt"></i>
            <span>${journeyInfo.route || 'Route'}</span>
        </div>
    `;
}

function updateSummaryPanel(selectedSeats, totalAmount) {
    const summaryPanel = document.querySelector('.summary-panel');
    if (!summaryPanel) return;

    const subtotal = parseFloat(totalAmount);
    const serviceFee = Math.round(subtotal * 0.05); // 5% service fee
    const total = subtotal + serviceFee;

    const summaryHTML = `
        <div class="journey-info">
            <!-- Journey info will be updated by updateJourneyInfo function -->
        </div>
        <div class="summary-row">
            <span>Total Kursi</span>
            <span>${selectedSeats.length} kursi</span>
        </div>
        <div class="summary-row">
            <span>Subtotal</span>
            <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
        </div>
        <div class="summary-row">
            <span>Biaya Layanan (5%)</span>
            <span>Rp ${serviceFee.toLocaleString('id-ID')}</span>
        </div>
        <div class="summary-row total">
            <span>Total Pembayaran</span>
            <span>Rp ${total.toLocaleString('id-ID')}</span>
        </div>
    `;

    summaryPanel.innerHTML = summaryHTML;
    updateJourneyInfo(JSON.parse(sessionStorage.getItem('journeyInfo') || '{}'));
}

function generatePassengerForms(selectedSeats) {
    const passengerList = document.querySelector('.passenger-list');
    if (!passengerList) return;

    passengerList.innerHTML = selectedSeats.map((seat, index) => `
        <div class="passenger-item">
            <div class="passenger-header">
                <h3 class="passenger-title">Penumpang ${index + 1}</h3>
                <span class="seat-number">Kursi ${seat.number}</span>
            </div>
            <div class="form-group">
                <label for="passenger_name_${index}">Nama Lengkap</label>
                <input type="text" 
                       id="passenger_name_${index}" 
                       name="passenger_name_${index}" 
                       required 
                       placeholder="Masukkan nama lengkap penumpang">
            </div>
            <div class="form-group">
                <label for="passenger_id_${index}">Nomor KTP/Paspor</label>
                <input type="text" 
                       id="passenger_id_${index}" 
                       name="passenger_id_${index}" 
                       required 
                       placeholder="Masukkan nomor KTP atau paspor">
            </div>
            <div class="form-group">
                <label for="passenger_type_${index}">Tipe Penumpang</label>
                <select id="passenger_type_${index}" 
                        name="passenger_type_${index}" 
                        required>
                    <option value="">Pilih tipe penumpang</option>
                    <option value="adult">Dewasa</option>
                    <option value="child">Anak-anak</option>
                    <option value="senior">Lansia</option>
                </select>
            </div>
        </div>
    `).join('');
}

function handleFormSubmit(e) {
    e.preventDefault();

    // Get user data
    const userData = {
        name: document.getElementById('user_name').value,
        email: document.getElementById('user_email').value,
        phone: document.getElementById('user_phone').value
    };

    // Get passenger data
    const selectedSeats = JSON.parse(sessionStorage.getItem('selectedSeats') || '[]');
    const passengers = selectedSeats.map((seat, index) => ({
        seatNumber: seat.number,
        name: document.getElementById(`passenger_name_${index}`).value,
        idNumber: document.getElementById(`passenger_id_${index}`).value,
        type: document.getElementById(`passenger_type_${index}`).value
    }));

    // Validate all required fields
    if (!validateForm(userData, passengers)) {
        alert('Mohon lengkapi semua data yang diperlukan');
        return;
    }

    // Store data in sessionStorage
    sessionStorage.setItem('userData', JSON.stringify(userData));
    sessionStorage.setItem('passengerData', JSON.stringify(passengers));

    // Redirect to payment page
    window.location.href = 'pembayaran.php';
}

function validateForm(userData, passengers) {
    // Validate user data
    if (!userData.name || !userData.email || !userData.phone) {
        return false;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(userData.email)) {
        return false;
    }

    // Validate phone number (minimal 10 digit)
    if (userData.phone.length < 10) {
        return false;
    }

    // Validate passenger data
    return passengers.every(passenger => 
        passenger.name && 
        passenger.idNumber && 
        passenger.type
    );
}

// Add input validation listeners
document.addEventListener('input', function(e) {
    if (e.target.matches('input[type="text"]')) {
        validateInput(e.target);
    }
});

function validateInput(input) {
    const value = input.value.trim();
    
    // Remove any non-alphanumeric characters except spaces and special characters for names
    if (input.id.includes('name')) {
        input.value = value.replace(/[^a-zA-Z\s\-']/g, '');
    }
    
    // Remove any non-numeric characters for ID numbers
    if (input.id.includes('id')) {
        input.value = value.replace(/\D/g, '');
    }
    
    // Format phone number
    if (input.id === 'user_phone') {
        input.value = value.replace(/\D/g, '');
        if (value.length > 13) {
            input.value = value.slice(0, 13);
        }
    }
} 