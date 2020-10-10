<?php
$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");
$resultArray = [];
while($row = mysqli_fetch_array($songQuery)) array_push($resultArray, $row['id']);

$jsonArray = json_encode($resultArray);
?>

<script>
$(document).ready(function() {
	let newPlaylist = <?php echo $jsonArray; ?>;
    audioElement = new Audio();
    setTimeout(() => {
        setTrack(newPlaylist[0], newPlaylist, false);
        updateVolumeProgressBar(audioElement.audio);
    }, 1000);

    $("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", ((e) => {
        e.preventDefault();
    }));

    $(".playbackBar .progressBar").mousedown(() => {
		mouseDown = true;
	});

	$(".playbackBar .progressBar").mousemove(function(e) {
		if(mouseDown) timeFromOffset(e, this);
	});

	$(".playbackBar .progressBar").mouseup(function(e) {
		timeFromOffset(e, this);
    });

    $(".volumeBar .progressBar").mousedown(() => {
		mouseDown = true;
	});

	$(".volumeBar .progressBar").mousemove(function(e) {
		if(mouseDown) {
            var volumePercentage = e.offsetX / $(this).width();
            if(volumePercentage >= 0 && volumePercentage <= 1) audioElement.audio.volume = volumePercentage;
        }
	});

	$(".volumeBar .progressBar").mouseup(function(e) {
		var volumePercentage = e.offsetX / $(this).width();
        if(volumePercentage >= 0 && volumePercentage <= 1) audioElement.audio.volume = volumePercentage;
	});

	$(document).mouseup(() => {
		mouseDown = false;
	});
});

function timeFromOffset(mouse, progressBar) {
	let percentage = mouse.offsetX / $(progressBar).width() * 100;
	let seconds = audioElement.audio.duration * (percentage / 100);
	audioElement.setTime(seconds);
}

function prevSong(){
    if(audioElement.audio.currentTime >= 3 || currentIndex == 0){
        audioElement.setTime(0);
    }else{
        currentIndex--;
        setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
    }
}

function nextSong(){
    if(repeat){
        audioElement.setTime(0);
        playSong();
        return;
    }
    if(currentIndex == currentPlaylist.length - 1) currentIndex = 0;
    else currentIndex++;

    let trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
    setTrack(trackToPlay, currentPlaylist, true);
}

function setRepeat(){
    repeat = !repeat;
    let repeatImageName = repeat ? "repeat-active.png" : "repeat.png";
    $(".controlButton.repeat img").attr("src", `assets/images/icons/${repeatImageName}`);
}

function setMute(){
    audioElement.audio.muted = !audioElement.audio.muted;
    let muteImageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
    $(".controlButton.volume img").attr("src", `assets/images/icons/${muteImageName}`);
}

function setShuffle(){
    shuffle = !shuffle;
    let shuffleImageName = shuffle ? "shuffle-active.png" : "shuffle.png";
    $(".controlButton.shuffle img").attr("src", `assets/images/icons/${shuffleImageName}`);

    if(shuffle){
        shuffleArray(shufflePlaylist);
        currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
    }else{
        currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
    }
}

function shuffleArray(playlist){
    for(let i=playlist.length; 1<i; i--) {
        let j = Math.floor(Math.random() * i);
        [playlist[j], playlist[i - 1]] = [playlist[i - 1], playlist[j]];
    }
}

function setTrack(trackId, newPlaylist, play){
    if(newPlaylist != currentPlaylist){
        currentPlaylist = newPlaylist;
        shufflePlaylist = currentPlaylist.slice();
        shuffleArray(shufflePlaylist);
    }

    if(shuffle) currentIndex = shufflePlaylist.indexOf(trackId);
    else currentIndex = currentPlaylist.indexOf(trackId);
    pauseSong();

    $.post("includes/handlers/ajax/getSongJson.php", {songId: trackId}, ((data) => {
        let track = JSON.parse(data);
        $(".trackName span").text(track.title);

        $.post("includes/handlers/ajax/getArtistJson.php", {artistId: track.artist}, ((data) => {
            let artist = JSON.parse(data);
            $(".artistName span").text(artist.name);
        }));

        $.post("includes/handlers/ajax/getAlbumJson.php", {albumId: track.album}, ((data) => {
            let album = JSON.parse(data);
            $(".albumLink img").attr("src", album.artworkPath);
        }));

        audioElement.setTrack(track);
        playSong();
    }));

    if(play){
        audioElement.play();
    }
}

function playSong(){
    if(audioElement.audio.currentTime == 0){
        $.post("includes/handlers/ajax/updatePlays.php", {songId: audioElement.currentlyPlaying.id});
    }

    $(".controlButton.play").hide();
    $(".controlButton.pause").show();
    audioElement.play();
}

function pauseSong(){
    $(".controlButton.play").show();
    $(".controlButton.pause").hide();
    audioElement.pause();
}
</script>

<div id="nowPlayingBarContainer">
    <div id="nowPlayingBar">

        <div id="nowPlayingLeft">
            <div class="content">
                <span class="albumLink">
                    <img src="" class="albumArtwork">
                </span>

                <div class="trackInfo">
                    <span class="trackName">
                        <span></span>
                    </span>
                    <span class="artistName">
                        <span></span>
                    </span>
                </div>

            </div>
        </div>

        <div id="nowPlayingCenter">
            <div class="content playerControls">

                <div class="buttons">
                    <button class="controlButton shuffle" title="shuffle button" onclick="setShuffle()">
                        <img src="assets/images/icons/shuffle.png" alt="Shuffle">
                    </button>

                    <button class="controlButton previous" title="previous button" onclick="prevSong()">
                        <img src="assets/images/icons/previous.png" alt="Previous">
                    </button>

                    <button class="controlButton play" title="play button" onclick="playSong()">
                        <img src="assets/images/icons/play.png" alt="Play">
                    </button>

                    <button class="controlButton pause" title="pause button" style="display: none;" onclick="pauseSong()">
                        <img src="assets/images/icons/pause.png" alt="Pause">
                    </button>

                    <button class="controlButton next" title="next button" onclick="nextSong()">
                        <img src="assets/images/icons/next.png" alt="Next">
                    </button>

                    <button class="controlButton repeat" title="repeat button" onclick="setRepeat()">
                        <img src="assets/images/icons/repeat.png" alt="Repeat">
                    </button>
                </div>

                <div class="playbackBar">
                    <span class="progressTime current">0.00</span>

                    <div class="progressBar">
                        <div class="progressBarBg">
                            <div class="progress"></div>
                        </div>
                    </div>

                    <span class="progressTime remaining">0.00</span>
                </div>

            </div>
        </div>

        <div id="nowPlayingRight">
            <div class="volumeBar">

                <button class="controlButton volume" title="Volume button" onclick="setMute()">
                    <img src="assets/images/icons/volume.png" alt="Volume">
                </button>

                <div class="progressBar">
                    <div class="progressBarBg">
                        <div class="progress"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>