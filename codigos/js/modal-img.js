document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('overlay');
    const overlayImage = document.getElementById('overlayImage');
    const closeBtn = document.getElementById('closeBtn');
    const rotateBtn = document.getElementById('rotateBtn');
    const flipBtn = document.getElementById('flipBtn');
    let currentRotation = 0;
    let isFlipped = false;

    // Função para abrir o overlay quando uma imagem for clicada
    document.querySelectorAll('.image-clickable').forEach((img, index) => {
        img.addEventListener('click', function () {
            // Mostra o overlay
            overlay.style.display = 'block';
            // Define a imagem do overlay para a mesma imagem clicada
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
