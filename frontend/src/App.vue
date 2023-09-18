<template>
    <div id="app">
        <h1>Jogo da Velha</h1>

        <div class="container">
            <div class="game-container">
                <div class="player-input">
                    <div class="input-row">
                        <label for="playerX">Jogador X:</label>

                        <input
                            :disabled="running"
                            id="playerX"
                            type="text"
                            v-model="playerX"
                        />
                    </div>

                    <div class="input-row">
                        <label for="playerO">Jogador O:</label>
                        
                        <input
                            :disabled="running"
                            id="playerO"
                            type="text"
                            v-model="playerO"
                        />
                    </div>
                </div>

                <div class="controls">
                    <button
                        :disabled="running"
                        @click="run"
                    >
                        Iniciar
                    </button>

                    <button
                        :disabled="!running"
                        @click="reset"
                    >
                        Reiniciar
                    </button>

                    <button @click="clean">
                        Limpar
                    </button>
                </div>

                <div v-if="running">
                    <p class="player-names">{{ playerX }} (X) vs {{ playerO }} (O)</p>

                    <div class="board">
                        <div
                            v-for="(cell, index) in board"
                            :class="{ 'x': cell === 'X', 'o': cell === 'O' }"
                            :key="index"
                            @click="play(index)"
                            class="cell"
                        >
                            {{ cell }}
                        </div>
                    </div>

                    <div
                        v-if="winner"
                        class="message"
                    >
                        <p>{{ winner }} venceu!</p>
                    </div>

                    <div
                        v-else-if="isTied"
                        class="message"
                    >
                        <p>O jogo empatou!</p>
                    </div>
                </div>
            </div>

            <div class="winners-list">
                <h2>Últimos Vencedores:</h2>

                <ul>
                    <li v-for="(game, index) in games" :key="index">
                        Jogador (X) {{game.player_x}},
                        Jogador (O) {{game.player_o}},
                        Vencedor {{game.winner}}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

const url = 'http://192.168.2.5:5001/api/games'

export default {
    name: 'App',
    computed: {
        isTied() {
            return this.board.every(cell => cell !== '');
        },
    },
    data() {
        return {
            board: Array(9).fill(''),
            games: [],
            player: 'X',
            playerO: '',
            playerX: '',
            running: false,
            winner: '',
        };
    },
    methods: {
        run() {
            if (this.playerX && this.playerO) {
                this.running = true;
            } else {
                alert('Os nomes dos jogadores são obrigatórios.');
            }
        },
        play(index) {
            if (!this.board[index] && !this.winner) {
                this.$set(this.board, index, this.player);
                this.checkWinner();
                this.player = this.player === 'X' ? 'O' : 'X';
            }
        },
        reset() {
            this.board = Array(9).fill('');
            this.player = 'X';
            this.winner = '';
        },
        clean() {
            this.reset();

            this.playerO = '';
            this.playerX = '';
            this.running = false;
        },
        loadGames() {
            axios.get(url)
                .then(({data}) => {
                    this.games = data;
                })
                .catch(() => {
                    alert('Erro ao carregar a lista de vencedores.')
                    this.clean()
                })
        },
        checkWinner() {
            const combinations = [
                [0, 1, 2],
                [3, 4, 5],
                [6, 7, 8],
                [0, 3, 6],
                [1, 4, 7],
                [2, 5, 8],
                [0, 4, 8],
                [2, 4, 6],
            ];

            for (const combination of combinations) {
                const [x, y, z] = combination;

                if (this.board[x] && this.board[x] === this.board[y] && this.board[x] === this.board[z]) {
                    this.winner = this.player === 'X' ? this.playerX : this.playerO;
                    this.storeGame();
                    break;
                }
            }
        },
        storeGame() {
            const request = {
                player_x: this.playerX,
                player_o: this.playerO,
                winner: this.winner,
            };

            axios.post(url, request)
                .then(() => this.loadGames())
                .catch(() => {
                    alert('Erro ao salvar o vencedor.')
                    this.clean()
                })
        },
    },
    mounted() {
        this.loadGames();
    },
}
</script>

<style>
body {
    background-color: #111;
    color: #fff;
    font-family: Arial, sans-serif;
    text-align: center;
}
#app {
    background-color: #222;
    border-radius: 8px;
    margin: 0 auto;
}
h1 {
    background-color: #333;
    font-size: 24px;
    margin: 0px;
    padding: 20px;
}
.container {
    display: flex;
    flex-direction: row;
}
.game-container {
    flex-grow: 2;
    margin-right: 20px;
    padding: 20px;
}
.player-input {
    display: flex;
    flex-direction: row;
    margin-bottom: 20px;
}
.input-row {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    text-align: left;
}
.input-row label {
    font-size: 16px;
    margin-bottom: 5px;
}
.input-row input {
    border-radius: 4px;
    border: 1px solid #333;
    font-size: 16px;
    padding: 5px;
    width: 100%;
}
.controls button {
    border: none;
    cursor: pointer;
    margin: 0px 5px;
    padding: 10px 20px;
}
.player-names {
    font-size: 18px;
    margin-bottom: 20px;
}
.board {
    display: grid;
    gap: 4px;
    grid-template-columns: repeat(3, 1fr);
    margin: 0 auto;
    width: 50%;
}
.cell {
    align-items: center;
    background-color: #333;
    cursor: pointer;
    display: flex;
    font-size: 36px;
    height: 160px;
    justify-content: center;
    transition: background-color 0.3s ease-in-out;
}
.cell:hover {
    background-color: #444;
}
.cell.x {
    color: #f00;
}
.cell.o {
    color: #00f;
}
.message {
    font-size: 24px;
    margin-top: 20px;
}
.message p {
    margin: 0px;
}
.winners-list {
    border: 1px solid #333;
    flex-grow: 1;
}
.winners-list h2 {
    font-size: 18px;
    margin-bottom: 10px;
}
.winners-list ul {
    list-style: none;
    padding: 0;
}
.winners-list li {
    font-size: 16px;
    margin-bottom: 5px;
}
@media (max-width: 800px) {
    .winners-list {
        display: none;
    }
    .player-input {
        flex-direction: column;
    }
    .board {
        width: 100%;
    }
}
</style>