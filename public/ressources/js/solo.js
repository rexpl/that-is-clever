const textRound = document.getElementById("script").getAttribute("textRound");
const textPoints = document.getElementById("script").getAttribute("textPoints");
const textConnection = document.getElementById("script").getAttribute("textConnection");

function window_resize() {
	
	if (window.innerWidth < 1000) {
		document.getElementById('main_container').innerHTML = 'Unsupported device.';
		return;
	}

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			if (this.status == 200) {
				document.getElementById('main_container').innerHTML = this.responseText;
				setDice(dices);
			}
		}
	}
	xhttp.open("GET", "/ressources/game/board/1000.min.html", true);
	xhttp.send();
}

var addEvent = function(object, type, callback) {
    if (object == null || typeof(object) == 'undefined') return;
    if (object.addEventListener) {
        object.addEventListener(type, callback, false);
    } else if (object.attachEvent) {
        object.attachEvent("on" + type, callback);
    } else {
        object["on"+type] = callback;
    }
};

addEvent(window, "resize", window_resize);
window_resize();

//https://stackoverflow.com/questions/784012/javascript-equivalent-of-phps-in-array
function inArray(needle, haystack) {
	var length = haystack.length;
	for(var i = 0; i < length; i++) {
		if(haystack[i] == needle) return true;
	}
	return false;
}

var board = [];
var dices = [];
var diceWhite = 0;
var diceBlue = 0;

var eventGame = 'game';

var socket = new WebSocket('wss://clever.mexenus.com:8080');
socket.onmessage = function(e) {
	message = JSON.parse(e.data);

	if (typeof message['bonus'] !== 'undefined') bonus(message['bonus']);

	if (typeof message['board'] !== 'undefined') {	
		board = message['board'];
		make_board();
	}

	if (typeof message['plus'] !== 'undefined') plus(message['plus']);
	if (typeof message['replay'] !== 'undefined') replay(message['replay']);
	if (typeof message['dice'] !== 'undefined') setDice(message['dice']);
	if (typeof message['used_dices'] !== 'undefined') setUsedDices(message['used_dices']);

	if (typeof message['round'] !== 'undefined') {
		return new_round(message['round']);
	}

	if (typeof message['finish'] !== 'undefined') finish(message['finish']);
};
socket.onclose = function() {
	alert(textConnection);
	window.location.replace("/pregame/solo");
}
socket.onerror = function(error) {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "/call/game/socket.error.php?q=" + encodeURI(JSON.stringify(error, ["message", "arguments", "type", "name"])), true);
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.send();
};

function finish(argument) {

	socket.onclose = function () {};
	socket.close();

	document.getElementById('myModal_end').style.display = 'block';
	document.getElementById('text_score').innerHTML = textPoints.replace("-{points}-", argument['total']);;

	setTimeout(function() {
		window.location.replace("/result/solo");
	}, 10000);
}

function new_round(argument) {
	document.getElementById('myModal_round').style.display = 'block';
	document.getElementById('text_round').innerHTML = textRound.replace("-{round}-", argument);
	document.getElementById('b-text-round').innerHTML = textRound.replace("-{round}-", argument);
	document.getElementById('myModal').style.opacity = 0;

	i = 0;
	while (i < 3) {
		i++;
		document.getElementById(i + '-dice-div').style.display = 'none';
	}

	setTimeout(function() {
		document.getElementById('myModal_round').style.display = 'none';
		document.getElementById('myModal').style.opacity = 1;
	}, 3000);
}

const diceColors = ['blue', 'yellow', 'green', 'orange', 'purple', 'white'];
const diceColorCode = ['#3284b8', '#dbc900', '#6bb058', '#dba100', '#8835cc', '#ffffff'];

function setUsedDices(argument) {

	i = 0;

	for (x in argument) {

		i++;
		index_color = diceColors.indexOf(x);

		document.getElementById(i + '-dice-div').style.backgroundColor = diceColorCode[index_color];
		document.getElementById(i + '-dice-div').style.display = 'inline-block';

		document.getElementById(i + '-dice-text').innerHTML = argument[x];

		if (x === 'white') {
			document.getElementById(i + '-dice-div').style.color = '#000000';
		}
		else {
			document.getElementById(i + '-dice-div').style.color = '#ffffff';
		}
	}

	if (i == 3) return;

	while (i < 3) {
		i++;
		document.getElementById(i + '-dice-div').style.display = 'none';
	}
}

async function setDice(argument) {
	dices = argument;

	for (x in diceColors) {
		div = document.getElementById(diceColors[x] + '-dice-div');

		div.classList.remove("animate__rollIn");

		if (typeof argument[diceColors[x]] === 'undefined') continue;

		text = document.getElementById(diceColors[x] + '-dice-text');

		text.innerHTML = argument[diceColors[x]];

		div.offsetWidth;
		div.setAttribute('draggable', true);
		div.style.visibility = 'visible';
		div.style.cursor = 'grab';
		div.style.opacity = 1;
		div.classList.add("animate__rollIn");

		if (diceColors[x] === 'blue') {
			diceBlue = argument[diceColors[x]];
		}
		if (diceColors[x] === 'white') {
			diceWhite = argument[diceColors[x]];
		}
	}
}

function hideDice(argument) {

	document.getElementById(argument + '-dice-div').style.visibility = 'hidden';
}

async function make_board() {

	makeBoardBlue(board['blue']);
	makeBoardYellow(board['yellow']);
	makeBoardGreen(board['green']);
	makeBoardOrange(board['orange']);
	makeBoardPurple(board['purple']);
	makePlusOne(board['p1']);
	makeReplay(board['re']);
}

function makeBoardBlue(argument) {
	
	for (x in argument) {
		text = document.getElementById('b-' + x + '-text');
		
		text.style.color = '#3284b8';
		text.style.fontFamily = 'system-ui';
		text.innerHTML = x;

		if (!argument[x]) continue;

		text.style.color = '#000000';
		text.style.fontFamily = 'PermanentMarker';
		text.innerHTML = 'X';
	}
}

function makeBoardYellow(argument) {
	
	for (x in argument) {
		text = document.getElementById('y-' + x + '-text');

		text.style.fontFamily = 'system-ui';
		text.innerHTML = x.charAt(1);

		if (!argument[x]) continue;

		text.style.fontFamily = 'PermanentMarker';
		text.innerHTML = 'X';
	}
}

const numbers_green = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
const text_green = ['≥1', '≥2', '≥3', '≥4', '≥5', '≥1', '≥2', '≥3', '≥4', '≥5', '≥6'];

function makeBoardGreen(argument) {
	
	for (x in numbers_green) {
		text = document.getElementById('g-' + numbers_green[x] + '-text');

		text.style.color = '#6bb058';
		text.style.fontFamily = 'system-ui';
		text.innerHTML = text_green[x];

		if (numbers_green[x] > argument) continue;

		text.style.color = '#000000';
		text.style.fontFamily = 'PermanentMarker';
		text.innerHTML = 'X';
	}
}

function makeBoardOrange(argument) {
	
	for (x in argument) {
		text = document.getElementById('o-' + x + '-text');

		text.style.color = '#dba100';
		text.style.fontFamily = 'system-ui';

		if (inArray(x, [4, 7, 9, 11])) {
			if (x == 11) {
				text.innerHTML = 'x3';
			}
			else {
				text.innerHTML = 'x2';
			}
		}
		else {
			text.innerHTML = '&nbsp;';
		}

		if (argument[x] == 0) continue;

		text.style.color = '#000000';
		text.style.fontFamily = 'PermanentMarker';
		text.innerHTML = argument[x];
	}
}

function makeBoardPurple(argument) {
	
	for (x in argument) {
		text = document.getElementById('p-' + x + '-text');

		text.style.color = '#a442f5';
		text.style.fontFamily = 'system-ui';

		tmp_index = x - 1;

		if (x == 1 || document.getElementById('p-' + tmp_index + '-text').innerHTML == 6) {
			text.innerHTML = '&nbsp;';
		}
		else {
			text.innerHTML = '<';
		}

		if (argument[x] == 0) continue;

		text.style.color = '#000000';
		text.style.fontFamily = 'PermanentMarker';
		text.innerHTML = argument[x];
	}
}

function makePlusOne(argument) {
	document.getElementById('plus_one_count').innerHTML = argument;
}

function makeReplay(argument) {
	document.getElementById('replay_count').innerHTML = argument;
}

async function bonus(argument) {
	let img = document.getElementById('modal_img');
	img.innerHTML = '';

	for (x in argument) {
		img.innerHTML += '<img class="m-3" src="/ressources/game/bonus-' + argument[x] +'.png" width="100" height="auto">';

		if (inArray(argument[x], [2,8,13,14])) {
			document.getElementById('myModal').style.display = 'block';
			return bonusChoice(argument[x]);
		}
	}
	
	document.getElementById('myModal').style.display = 'block';
}

var bonusChoiceID = 0;

function bonusChoice(argument) {
	bonusChoiceID = argument;
	eventGame = 'bonus';

	switch (argument) {
		case 2:
			dice = {yellow: 'X'}
		break;
		case 8:
			dice = {blue: 'X'}
		break;
		case 13:
			dice = {white: 'X'}
		break;
		case 14:
			dice = {white: 6}
		break;
	}

	setDice(dice);
}

async function plus(argument) {
	div = document.getElementById('plus_one');

	if (board['p1'] > 0 && argument && eventGame !== 'bonus') {
		div.style.opacity = 1;
		div.style.cursor = 'pointer';
	}
	else {
		div.style.opacity = 0.6;
		div.style.cursor = 'not-allowed';
	}
}

async function replay(argument) {
	div = document.getElementById('replay');

	if (board['re'] > 0 && argument && eventGame !== 'bonus') {
		div.style.opacity = 1;
		div.style.cursor = 'pointer';
	}
	else {
		div.style.opacity = 0.6;
		div.style.cursor = 'not-allowed';
	}
}

function reply_click(id) {
	div = document.getElementById(id);

	if (div.style.opacity != 1) return false;

	if (id === 'replay') {
		socket.send('{"bonus":"replay"}');
	}
	if (id === 'plus_one') {
		socket.send('{"bonus":"plus1"}');
		eventGame = 'plus1';
	}
}

function getDiceColor(first_letter) {
	switch (first_letter) {
		case 'b':
			return 'blue';
		break;
		case 'y':
			return 'yellow';
		break;
		case 'g':
			return 'green';
		break;
		case 'o':
			return 'orange';
		break;
		case 'p':
			return 'purple';
		break;
	}
}

var possible_values = [];
var selected_dice;

function reply_drop(event, id) {

	switch (eventGame) {
		case 'game':
			send = { 
				choice: {
					position: id.split("-")[1],
					color: getDiceColor(id.split("-")[0]),
					dice: selected_dice
				}
			}
		break;
		case 'plus1':
			send = { 
				p1_choice: {
					position: id.split("-")[1],
					color: getDiceColor(id.split("-")[0]),
					dice: selected_dice
				}
			}

			eventGame = 'game';
		break;
		case 'bonus':
			send = { 
				bonus_choice: {
					position: id.split("-")[1],
					color: getDiceColor(id.split("-")[0]),
					id: bonusChoiceID
				}
			}

			eventGame = 'game';
		break;
	}

	socket.send(JSON.stringify(send));

	for (x in dices) {
		hideDice(x);
	}
}

function reply_dragstart(event, id) {

	if (eventGame === 'bonus') return replay_dragstartBonus(event, id);

	selected_dice = id.split("-")[0];

	if (dices[selected_dice] === undefined) return false;

	switch (selected_dice) {
		case 'blue':

			if (!board['blue'][dices['blue'] + diceWhite]) possible_values.push('b-' + (dices['blue'] + diceWhite) + '-div');

		break;
		case 'yellow':

			if (!board['yellow'][dices['yellow'] + 10]) possible_values.push('y-' + (dices['yellow'] + 10) + '-div');
			if (!board['yellow'][dices['yellow'] + 20]) possible_values.push('y-' + (dices['yellow'] + 20) + '-div');

		break;
		case 'green':

			green_value = (board['green'] >= 5) ? board['green'] - 5 : board['green'];
			if (dices['green'] >= green_value && board['green'] != 11)  possible_values.push('g-' + (board['green'] + 1) + '-div');

		break;
		case 'orange':

			if (board['orange_position'] != 11)  possible_values.push('o-' + (board['orange_position'] + 1) + '-div');

		break;
		case 'purple':

			if (board['last_purple_value'] < dices['purple'] && board['purple_position'] != 11)  possible_values.push('p-' + (board['purple_position'] + 1) + '-div');

		break;
		default: //white dice

			if (!board['blue'][diceBlue + dices['white']]) possible_values.push('b-' + (diceBlue + dices['white']) + '-div');

			if (!board['yellow'][dices['white'] + 10]) possible_values.push('y-' + (dices['white'] + 10) + '-div');
			if (!board['yellow'][dices['white'] + 20]) possible_values.push('y-' + (dices['white'] + 20) + '-div');

			green_value = (board['green'] >= 5) ? board['green'] - 5 : board['green'];
			if (dices['white'] >= green_value && board['green'] != 11)  possible_values.push('g-' + (board['green'] + 1) + '-div');

			if (board['orange_position'] != 11)  possible_values.push('o-' + (board['orange_position'] + 1) + '-div');

			if (board['last_purple_value'] < dices['white'] && board['purple_position'] != 11)  possible_values.push('p-' + (board['purple_position'] + 1) + '-div');

		break;
	}
}

function replay_dragstartBonus(event, id) {

	switch (bonusChoiceID) {
		case 2:

			for (x in board['yellow']) {

				if (board['yellow'][x]) continue;

				possible_values.push('y-' + x + '-div');
			}

		break;
		case 8:

			for (x in board['blue']) {

				if (board['blue'][x]) continue;

				possible_values.push('b-' + x + '-div');
			}

		break;
		case 13:

			for (x in board['yellow']) {

				if (board['yellow'][x]) continue;

				possible_values.push('y-' + x + '-div');
			}

			for (x in board['blue']) {

				if (board['blue'][x]) continue;

				possible_values.push('b-' + x + '-div');
			}

			if (board['green'] != 11) possible_values.push('g-' + (board['green'] + 1) + '-div');
			
		break;
		case 14:
			
			if (board['orange_position'] != 11) possible_values.push('o-' + (board['orange_position'] + 1) + '-div');
			if (board['purple_position'] != 11) possible_values.push('p-' + (board['purple_position'] + 1) + '-div');

		break;
	}
}

function allowDrop(event, id) {
	if (inArray(id, possible_values)) {
		event.preventDefault();
	}
}