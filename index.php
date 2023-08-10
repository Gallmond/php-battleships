<?php
set_time_limit(0);

require __DIR__.'/vendor/autoload.php';

use Gavin\GuestlineBattleships\Api\Api;
use Gavin\GuestlineBattleships\database\DataStore;

if(Api::isPost()){
    $api = new Api(new DataStore());
    $api->handleRequest();
}
?>
<html>
    <head>
        <title>Gavin's cool battleship game</title>
        <link href="./styles.css" rel="stylesheet" >
    </head>
    <body>
        <div class="game-grid">
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
                <div class="grid-cell"></div>
            </div>
        </div>

        <div class="controls">
            <button id="debug-button">debug</button>
            <button id="new-game">New game</button>
            <button id="load-game">Load game</button>
            <input id="game-id" type="text" placeholder="game-id" />
            <button id="attack-cell">Attack Cell:</button>
            <input id="target-cell" placeholder="A5">
        </div>

        <script type="application/javascript">
            const debugButton = document.getElementById('debug-button')
            const newGameButton = document.getElementById('new-game')
            const loadGameButton = document.getElementById('load-game')
            const gameIdInput = document.getElementById('game-id')
            const attackCellButton = document.getElementById('attack-cell')
            const targetCellInput = document.getElementById('target-cell')

            const getCellElement = (x, y) => {
                const allCells = document.getElementsByClassName('grid-cell')
                for(const cell of allCells){
                    if(
                        x.toString() === cell.getAttribute('x')
                        && y.toString() === cell.getAttribute('y')
                    ){
                        return cell
                    }
                }

                return null
            }

            const updateGrid = (attacks) => {
                Object.entries(attacks).forEach(keyVal => {
                    const [key, val] = keyVal
                    const keyParts = key.split(':')
                    const [x, y] = keyParts

                    const cell = getCellElement(x, y)

                    if(val === 'miss'){
                        cell.innerHTML = '❌'
                    }
                    if(val === 'hit'){
                        cell.innerHTML = '💥'
                    }
                })
            }

            const sendPost = async (data) => {
                const res = await fetch('/', {
                    method: 'post',
                    body: JSON.stringify(data)
                });

                return await res.json()
            }

            const debug = async () => {
                const responseData = await sendPost({
                    action: 'debug',
                    'game-id': gameIdInput.value
                })

                console.debug({debug: responseData})
            }

            const attackByCell = async () => {
                const letters = targetCellInput.value

                const [x, y] = lettersToCord(letters)

                if(x === -1 || y === -1){
                    alert('invalid targert')
                    targetCellInput.value = ''

                    return
                }

                attack(x,y)

                targetCellInput.value = ''
            }

            const attack = async (x, y) => {
                const gameId = gameIdInput.value
                const responseData = await sendPost({
                    'game-id': gameId,
                    'action' : 'attack',
                    x, y
                })

                updateGrid(responseData['attacks'])

                if(responseData['game-over'] === true){
                    alert('you win!!!')
                }
            }

            const loadGame = async () => {
                const responseData = await sendPost({
                    'game-id': gameIdInput.value,
                    action: 'new-game'
                })

                updateGrid(responseData['attacks'])
            }

            const newGame = async () => {
                const arr = new Uint16Array(2)
                window.crypto.getRandomValues(arr)
                const gameId = arr.join('')

                gameIdInput.value = gameId

                const responseData = await sendPost({
                    'game-id': gameId,
                    action: 'new-game'
                })

                initialiseGrid()
                updateGrid(responseData['attacks'])
                gameIdInput.value = responseData['game-id']
            }

            const initialiseGrid = () => {
                const rows = document.getElementsByClassName('grid-row')
                const numRows = rows.length;

                for(let y = 0; y < numRows; y++){
                    const cells = rows[y].getElementsByClassName('grid-cell')
                    const numCells = cells.length

                    for(let x = 0; x < numCells; x++){
                        const cell = cells[x]
                        cell.setAttribute('x', x.toString())
                        cell.setAttribute('y', y.toString())
                        cell.innerHTML = coordToLetters(x,y) + '<br>'

                        const newButton = document.createElement('button');
                        newButton.setAttribute('x', x.toString());
                        newButton.setAttribute('y', y.toString());
                        newButton.addEventListener('click', event => {
                            const x = event.currentTarget.getAttribute('x')
                            const y = event.currentTarget.getAttribute('y')

                            attack(x, y)
                        })
                        newButton.innerHTML = `⌖`

                        cell.append(newButton)
                    }
                }
            }

            const lettersToCord = (target) => {
                const first = target.substring(0, 1)
                const last = target.substring(1, 2)

                const letters = 'ABCDEFGHIJ'.split('');
                const numbers = [1,2,3,4,5,6,7,8,9,10]

                let x = letters.indexOf(first.toUpperCase())
                let y = numbers.indexOf(parseInt(last))

                return [x, y]
            }

            const coordToLetters = (x, y) => {
                const letters = 'ABCDEFGHIJ'.split('');
                const numbers = [1,2,3,4,5,6,7,8,9,10]

                return `${letters[x]}${numbers[y]}`
            }

            document.addEventListener('DOMContentLoaded', () => {
                debugButton.addEventListener('click', debug)
                newGameButton.addEventListener('click', newGame);
                loadGameButton.addEventListener('click', loadGame)
                attackCellButton.addEventListener('click', attackByCell)

                initialiseGrid();
            })

        </script>
    </body>
</html>
