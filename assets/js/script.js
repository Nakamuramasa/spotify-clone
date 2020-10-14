var currentPlaylist = [];
var shufflePlaylist = [];
var tempPlaylist = [];
var audioElement;
var mouseDown = false;
var currentIndex = 0;
var repeat = false;
var shuffle = false;
var userLoggedIn;
var timer;

$(document).click((click) => {
    let target = $(click.target);
    if(!target.hasClass("item") && !target.hasClass("optionsButton")){
        hideOptionsMenu();
    }
});

$(window).scroll(() => {
    hideOptionsMenu();
});

$(document).on("change", "select.playlist", function() {
	let select = $(this);
	let playlistId = select.val();
    let songId = select.prev(".songId").val();

	$.post("includes/handlers/ajax/addToPlaylist.php", { playlistId: playlistId, songId: songId})
	.done(function(error) {

		if(error != "") {
			alert(error);
			return;
		}

		hideOptionsMenu();
		select.val("");
	});
});

function openPage(url){
    if(timer != null) clearTimeout(timer);

    if(url.indexOf("?") == -1) url = url + "?";
	let encodedUrl = encodeURI(`${url}&userLoggedIn=${userLoggedIn}`);
    $("#mainContent").load(encodedUrl);
    $("body").scrollTop(0);
    history.pushState(null, null, url);
}

function removeFromPlaylist(button, playlistId){
    let songId = $(button).prevAll(".songId").val();

    $.post("includes/handlers/ajax/removeFromPlaylist.php", {playlistId: playlistId, songId: songId})
    .done((error) => {
        if(error != ""){
            alert(error);
            return;
        }
        openPage(`playlist.php?id=${playlistId}`);
    });
}

function createPlaylist(){
    let popup = prompt("Please enter the name of your playlist");
    if(popup){
        $.post("includes/handlers/ajax/createPlaylist.php", {name: popup, username: userLoggedIn})
        .done((error) => {
            if(error != ""){
                alert(error);
                return;
            }
            openPage("yourMusic.php");
        });
    }
}

function deletePlaylist(playlistId){
    let prompt = confirm("Are you sure you want to delete this playlist?");
    if(prompt){
        $.post("includes/handlers/ajax/deletePlaylist.php", {playlistId: playlistId})
        .done((error) => {
            if(error != ""){
                alert(error);
                return;
            }
            openPage("yourMusic.php");
        });
    }
}

function hideOptionsMenu(){
    let menu = $(".optionsMenu");
    if(menu.css("display") != "none"){
        menu.css("display", "none");
    }
}

function showOptionsMenu(button) {
    let songId = $(button).prevAll(".songId").val();
	let menu = $(".optionsMenu");
    let menuWidth = menu.width();
    menu.find(".songId").val(songId);

	let scrollTop = $(window).scrollTop();
	let elementOffset = $(button).offset().top;

	let top = elementOffset - scrollTop;
	let left = $(button).position().left;

	menu.css({ "top": top + "px", "left": left - menuWidth + "px", "display": "inline" });
}

function formatTime(durationSeconds){
    let time = Math.round(durationSeconds);
    let minutes = Math.floor(time/60);
    let seconds = String(time-(minutes*60)).padStart(2, "0");
    return `${minutes}:${seconds}`;
}

function updateTimeProgressBar(audio){
    $(".progressTime.current").text(formatTime(audio.currentTime));
    $(".progressTime.remaining").text(formatTime(audio.duration - audio.currentTime));

    let progress = audio.currentTime / audio.duration * 100;
    $(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio){
    let volume = audio.volume * 100;
    $(".volumeBar .progress").css("width", volume + "%");
}

function playFirstSong(){
    setTrack(tempPlaylist[0], tempPlaylist, true);
}

function Audio() {
	this.currentlyPlaying;
    this.audio = document.createElement('audio');

    this.audio.addEventListener("ended", (() => {
        nextSong();
    }));

    this.audio.addEventListener("canplay", function(){
        let duration = formatTime(this.duration)
        $(".progressTime.remaining").text(duration);
    });

    this.audio.addEventListener("timeupdate", function(){
        if(this.duration) updateTimeProgressBar(this);
    });

    this.audio.addEventListener("volumechange", function(){
        updateVolumeProgressBar(this);
    });

	this.setTrack = ((track) => {
        this.currentlyPlaying = track;
		this.audio.src = track.path;
    });

    this.play = (() => {
        this.audio.play();
    });

    this.pause = (() => {
        this.audio.pause();
    });

    this.setTime = function(seconds) {
		this.audio.currentTime = seconds;
	}
}