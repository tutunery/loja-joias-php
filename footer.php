<style>
    /* Garante que o footer siga o tema escuro/claro do Bootstrap */
    footer.site-footer {
        background-color: var(--bs-tertiary-bg); /* Cor de fundo automática do tema */
        color: var(--bs-body-color);            /* Cor do texto automática */
        border-top: 1px solid var(--bs-border-color);
        padding-top: 2rem;
        padding-bottom: 2rem;
        margin-top: auto; /* Isso empurra o footer para baixo */
        width: 100%;
    }
</style>

<footer class="site-footer">
    <div class="container text-center text-md-start">
        <div class="row">
            
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4 text-warning">Lumière Joias</h6>
                <p>Elegância e sofisticação em cada detalhe. Transformando metais nobres em memórias eternas.</p>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4 text-warning">Links</h6>
                <p><a href="index.php" class="text-reset text-decoration-none">Home</a></p>
                <p><a href="perfil.php" class="text-reset text-decoration-none">Minha Conta</a></p>
                <p><a href="#" class="text-reset text-decoration-none">Sobre Nós</a></p>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4 text-warning">Contato</h6>
                <p><i class="fas fa-home me-3"></i> Rio de janeiro, Rj</p>
                <p><i class="fas fa-envelope me-3"></i> contato@lumiere.com</p>
            </div>
            
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4 text-warning">Redes</h6>
                <a href="https://www.instagram.com/nerytutuu/?next=%2F" class="text-reset me-3 fs-5"><i class="fab fa-instagram"></i></a>
                <a href="https://www.linkedin.com/in/luiz-arthur-nery-leite-910580239/" class="text-reset me-3 fs-5"><i class="fab fa-linkedin"></i></a>
                <a href="https://github.com/tutunery" class="text-reset fs-5"><i class="fab fa-github"></i></a>
            </div>
        </div>

        <div class="text-center pt-3 border-top border-secondary">
            © <?php echo date('Y'); ?> <strong>Lumière Joias</strong>. Todos os direitos reservados.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>