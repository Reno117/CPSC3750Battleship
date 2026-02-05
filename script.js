const board = document.getElementById("board");
const statusText = document.getElementById("status");
document.getElementById("newGame").addEventListener("click", () => {
  fetch("game.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "newGame" })
  })
  .then(res => res.json())
  .then(() => {
    document.querySelectorAll(".cell").forEach(cell => {
      cell.className = "cell";
      cell.style.pointerEvents = "auto";
    });
    statusText.textContent = "Click a cell to fire!";
  });
});


for (let i = 0; i < 64; i++) {
  const cell = document.createElement("div");
  cell.className = "cell";
  cell.dataset.index = i;

  cell.addEventListener("click", () => fire(cell));
  board.appendChild(cell);
}

function fire(cell) {
  fetch("game.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ shot: cell.dataset.index })
  })
  .then(res => res.json())
  .then(data => {
    cell.classList.add(data.result);
    cell.style.pointerEvents = "none";
    statusText.textContent = data.result === "hit" ? "Hit!" : "Miss!";

    if (data.gameOver) {
      statusText.textContent = "Game Over — You Win!";
      disableBoard();
    } else if (data.aiShot !== undefined) {
      // AI fires back
      const aiCell = document.querySelector(`.cell[data-index='${data.aiShot.index}']`);
      aiCell.classList.add(data.aiShot.result);
      statusText.textContent += ` AI fired at ${data.aiShot.index}: ${data.aiShot.result}`;
      if (data.aiGameOver) {
        statusText.textContent = "Game Over — AI Wins!";
        disableBoard();
      }
    }
  });
}

function disableBoard() {
  document.querySelectorAll(".cell").forEach(c => c.style.pointerEvents = "none");
}
