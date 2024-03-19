init();

	function init() {
		initProperties();

		eventActivateCamera()
		    .eventBtnCapture();
	}

	function initProperties() {
		$captureVideo = document.querySelector('#captureVideo');
		$canvas = document.querySelector('#canvas');
		canvasContext = $canvas.getContext('2d');

		$btnCapture = $('#btnCapture');
		$errorMessage = $('#errorMessage');
		dataURL = '';

		$activateCamera = $('#activateCamera');
		$checkedCamera = $activateCamera.find('#checkedCamera');

		iconCheck = '<i class="fa fa-check text-navy"></i>';
		iconTimes = '<i class="fa fa-times text-danger"></i>';

		constraints = {
			audio: false,
			video: {
				facingMode: 'environment'
			},
		};

		errorName = {
			webcam: null,
		};
	}

	async function userMedia() {
		try {
			stream = await navigator.mediaDevices.getUserMedia(constraints);
			successStream();
		}

		catch(error) {
			if(error.name === 'NotAllowedError') {
				errorName.webcam = error.name;
				showErrors();
			} else {
				$errorMessage.html(error.toString());
			}

			$checkedCamera.html(iconTimes);
		}
	}

	function successStream() {
		$captureVideo.srcObject = stream;
		$checkedCamera.html(iconCheck);
	}

	function showErrors() {
		if(!stream){
			$errorMessage.html("Untuk mengambil gambar, pastikan anda mengizinkan kamera untuk aktif.");
			return true;
		}

		if(errorName.webcam === 'NotAllowedError') {
			$errorMessage.html("Untuk melakukan absen, pastikan anda mengizinkan kamera untuk aktif.");
			return true;
		}

		return false;
	}

	function eventActivateCamera() {
		$activateCamera.on('click', function(){
			userMedia();
		});

		return this;
	}

	function eventBtnCapture() {
		$btnCapture.on('click', function(){
			if(showErrors()) return;

			const captureVideoWidth = $captureVideo.offsetWidth;
			const captureVideoHeight = $captureVideo.offsetHeight;

			$canvas.width  = captureVideoWidth;
			$canvas.height = captureVideoHeight;

			canvasContext.drawImage($captureVideo, 0, 0, captureVideoWidth, captureVideoHeight);

			if($(this).hasClass('capture')) {
				$captureVideo.pause();
				dataURL = $canvas.toDataURL('image/jpeg', 0.9);

				$(this).html('<i class="fa fa-refresh"></i> Ulang');
			}

			if($(this).hasClass('recapture')) {
				$captureVideo.play();
				dataURL = '';

				$(this).html('<i class="fa fa-camera"></i> Ambil');
			}

			$(this).toggleClass('capture recapture');
			$errorMessage.html('');
		});

		return this;
	}

	function resetStream() {
		dataURL = '';
		$captureVideo.pause();
		$captureVideo.src = "";

		const tracks = stream.getTracks();
		tracks[0].stop();

		stream.removeTrack(tracks[0]);
	}
