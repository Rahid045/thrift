<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="section">
    <div class="container contact-grid">
        <div class="contact-card">
            <span class="eyebrow">Get in touch</span>
            <h1>Talk to Ramo</h1>
            <p>For product availability, custom requests, and order enquiries, contact us instantly through WhatsApp.</p>
            <a class="whatsapp-button" href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo rawurlencode('Hello Thrift with Ramo, I would like to place an order.'); ?>" target="_blank" rel="noopener noreferrer">Message us on WhatsApp</a>
        </div>

        <div class="contact-card">
            <h3>Business info</h3>
            <ul class="contact-list">
                <li><strong>Business:</strong> Thrift with Ramo</li>
                <li><strong>WhatsApp:</strong> <?php echo WHATSAPP_NUMBER; ?></li>
                <li><strong>Currency:</strong> UGX – Ugandan Shillings</li>
                <li><strong>Availability:</strong> Heels and boots</li>
            </ul>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
