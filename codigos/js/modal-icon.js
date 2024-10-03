document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('overlay');
    const overlayImage = document.getElementById('overlayImage');
    const closeBtn = document.getElementById('closeBtn');
    const rotateBtn = document.getElementById('rotateBtn');
    const flipBtn = document.getElementById('flipBtn');
    let currentRotation = 0;
    let isFlipped = false;

    // Função para abrir o overlay quando um ícone for clicado
    document.querySelectorAll('.camera-icon').forEach((icon, index) => {
        icon.addEventListener('click', function () {
            // Mostra o overlay
            overlay.style.display = 'block';
            // Define a imagem do overlay para a imagem correspondente ao ícone clicado
            const imagePath = document.querySelectorAll('.image-path')[index].value;
            overlayImage.src = imagePath;
            // Reinicializa a rotação e flip
            currentRotation = 0;
            isFlipped = false;
            overlayImage.style.transform = 'rotate(0deg) scaleX(1)';
        });
    });

    // Função para fechar o overlay
    closeBtn.addEventListener('click', function () {
        overlay.style.display = 'none';
    });

    // Função para rotacionar a imagem
    rotateBtn.addEventListener('click', function () {
        currentRotation = (currentRotation + 90) % 360;
        overlayImage.style.transform = `rotate(${currentRotation}deg) scaleX(${isFlipped ? -1 : 1})`;
    });

    // Função para espelhar a imagem
    flipBtn.addEventListener('click', function () {
        isFlipped = !isFlipped;
        overlayImage.style.transform = `rotate(${currentRotation}deg) scaleX(${isFlipped ? -1 : 1})`;
    });
});
