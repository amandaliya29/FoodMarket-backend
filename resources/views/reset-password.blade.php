<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Food Market</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: "Poppins", serif;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            width: 100%;
        }

        body {
            display: grid;
            place-items: center;
        }

        .main-container {
            max-width: 450px;
            width: 100%;
            height: 100%;
            padding: 30px 16px;
            /* border: 1px solid red; */
            display: flex;
            flex-direction: column;
        }

        .head {
            font-size: 22px;
            font-weight: 500;
            line-height: 33px;
        }

        .slogan {
            font-size: 14px;
            font-weight: 300;
            line-height: 21px;
            text-align: left;
            color: #8D92A3;
        }

        .form-div {
            padding: 16px 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            /* border: 1px solid blue; */
            justify-content: center;
        }

        label {
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            text-align: left;
        }

        .input-div {
            width: 100%;
            border: 1px solid #02020247;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            margin-top: 6px;
        }

        .input-div input {
            width: 100%;
            border: none;
            background: none;
            padding: 10px;
            outline: none;
        }

        .input-div button {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            top: 10%;
            background: none;
            border: none;
            padding: 0 12px;
        }

        .input-div button svg {
            width: 22px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #EB0029;
            border: none;
            color: white;
            font-family: Poppins;
            font-size: 14px;
            font-weight: 500;
            line-height: 21px;
            border-radius: 8px;
        }

        .login-text {
            text-align: center;
            font-size: 14px;
            /* color: #EB0029; */
        }

        .login-text p:last-child {
            font-size: 12px;
        }

        .alert-message {
            color: red;
            font-size: 13px;
            margin-top: 4px;
        }

        .display-none {
            display: none;
        }

        .action-message {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .action-message svg {
            max-width: 60px;
        }

        .action-message .message {
            font-size: 22px;
            font-weight: 700;
            margin-top: 14px;
            text-align: center;
        }

        .action-message .text {
            font-size: 14px;
            font-weight: 500;
            text-align: center;
        }

        .action-success {
            fill: #4BB543
        }

        .action-error {
            fill: #EB0029;
        }

        @media screen and (max-width: 699px){
            .action-message .message {
                font-size: 18px;
                font-weight: 600;
            }

            .action-message .text {
                font-size: 12px;
            } 
        }
    </style>
</head>

<body>
    <div class="main-container">
        @if (isset($status) && isset($message) && isset($text))
        <div class="action-message">
            @if ($status)
            <svg xmlns="http://www.w3.org/2000/svg" class="action-success" viewBox="0 0 512 512">
                <path
                    d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z" />
            </svg>
            @else
            <svg xmlns="http://www.w3.org/2000/svg" class="action-error" viewBox="0 0 512 512">
                <path
                    d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24l0 112c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-112c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
            </svg>
            @endif
            <p class="message">{{ $message }}</p>
            <p class="text">{{ $text }}</p>
        </div>
        @else

        <h1 class="head">Reset Password</h1>
        <p class="slogan">Reset Your Password</p>

        <div class="form-div">
            <form action="{{ route('password.reset', ['token' => $token]) }}" method="POST">
                @csrf
                <div>
                    <label for="password">New Password</label>
                    <div class="input-div">
                        <input type="password" name="password" placeholder="Password" id="password">
                        <button type="button" id="password_show_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                <path
                                    d="M288 80c-65.2 0-118.8 29.6-159.9 67.7C89.6 183.5 63 226 49.4 256c13.6 30 40.2 72.5 78.6 108.3C169.2 402.4 222.8 432 288 432s118.8-29.6 159.9-67.7C486.4 328.5 513 286 526.6 256c-13.6-30-40.2-72.5-78.6-108.3C406.8 109.6 353.2 80 288 80zM95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c46.8 43.5 78.1 95.4 93 131.1c3.3 7.9 3.3 16.7 0 24.6c-14.9 35.7-46.2 87.7-93 131.1C433.5 443.2 368.8 480 288 480s-145.5-36.8-192.6-80.6C48.6 356 17.3 304 2.5 268.3c-3.3-7.9-3.3-16.7 0-24.6C17.3 208 48.6 156 95.4 112.6zM288 336c44.2 0 80-35.8 80-80s-35.8-80-80-80c-.7 0-1.3 0-2 0c1.3 5.1 2 10.5 2 16c0 35.3-28.7 64-64 64c-5.5 0-10.9-.7-16-2c0 .7 0 1.3 0 2c0 44.2 35.8 80 80 80zm0-208a128 128 0 1 1 0 256 128 128 0 1 1 0-256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="display-none" viewBox="0 0 640 512">
                                <path
                                    d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zm151 118.3C226 97.7 269.5 80 320 80c65.2 0 118.8 29.6 159.9 67.7C518.4 183.5 545 226 558.6 256c-12.6 28-36.6 66.8-70.9 100.9l-53.8-42.2c9.1-17.6 14.2-37.5 14.2-58.7c0-70.7-57.3-128-128-128c-32.2 0-61.7 11.9-84.2 31.5l-46.1-36.1zM394.9 284.2l-81.5-63.9c4.2-8.5 6.6-18.2 6.6-28.3c0-5.5-.7-10.9-2-16c.7 0 1.3 0 2 0c44.2 0 80 35.8 80 80c0 9.9-1.8 19.4-5.1 28.2zm9.4 130.3C378.8 425.4 350.7 432 320 432c-65.2 0-118.8-29.6-159.9-67.7C121.6 328.5 95 286 81.4 256c8.3-18.4 21.5-41.5 39.4-64.8L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5l-41.9-33zM192 256c0 70.7 57.3 128 128 128c13.3 0 26.1-2 38.2-5.8L302 334c-23.5-5.4-43.1-21.2-53.7-42.3l-56.1-44.2c-.2 2.8-.3 5.6-.3 8.5z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <span class="alert-message">{{ $message }}</span>
                    @enderror
                </div>
                <div style="margin-top: 16px;">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-div">
                        <input type="password" name="confirm_password" placeholder="Confirm Password"
                            id="confirm_password">
                        <button type="button" id="confirm_password_show_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                <path
                                    d="M288 80c-65.2 0-118.8 29.6-159.9 67.7C89.6 183.5 63 226 49.4 256c13.6 30 40.2 72.5 78.6 108.3C169.2 402.4 222.8 432 288 432s118.8-29.6 159.9-67.7C486.4 328.5 513 286 526.6 256c-13.6-30-40.2-72.5-78.6-108.3C406.8 109.6 353.2 80 288 80zM95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c46.8 43.5 78.1 95.4 93 131.1c3.3 7.9 3.3 16.7 0 24.6c-14.9 35.7-46.2 87.7-93 131.1C433.5 443.2 368.8 480 288 480s-145.5-36.8-192.6-80.6C48.6 356 17.3 304 2.5 268.3c-3.3-7.9-3.3-16.7 0-24.6C17.3 208 48.6 156 95.4 112.6zM288 336c44.2 0 80-35.8 80-80s-35.8-80-80-80c-.7 0-1.3 0-2 0c1.3 5.1 2 10.5 2 16c0 35.3-28.7 64-64 64c-5.5 0-10.9-.7-16-2c0 .7 0 1.3 0 2c0 44.2 35.8 80 80 80zm0-208a128 128 0 1 1 0 256 128 128 0 1 1 0-256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="display-none" viewBox="0 0 640 512">
                                <path
                                    d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zm151 118.3C226 97.7 269.5 80 320 80c65.2 0 118.8 29.6 159.9 67.7C518.4 183.5 545 226 558.6 256c-12.6 28-36.6 66.8-70.9 100.9l-53.8-42.2c9.1-17.6 14.2-37.5 14.2-58.7c0-70.7-57.3-128-128-128c-32.2 0-61.7 11.9-84.2 31.5l-46.1-36.1zM394.9 284.2l-81.5-63.9c4.2-8.5 6.6-18.2 6.6-28.3c0-5.5-.7-10.9-2-16c.7 0 1.3 0 2 0c44.2 0 80 35.8 80 80c0 9.9-1.8 19.4-5.1 28.2zm9.4 130.3C378.8 425.4 350.7 432 320 432c-65.2 0-118.8-29.6-159.9-67.7C121.6 328.5 95 286 81.4 256c8.3-18.4 21.5-41.5 39.4-64.8L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5l-41.9-33zM192 256c0 70.7 57.3 128 128 128c13.3 0 26.1-2 38.2-5.8L302 334c-23.5-5.4-43.1-21.2-53.7-42.3l-56.1-44.2c-.2 2.8-.3 5.6-.3 8.5z" />
                            </svg>
                        </button>
                    </div>
                    @error('confirm_password')
                    <span class="alert-message">{{ $message }}</span>
                    @enderror
                </div>
                <div style="margin-top: 24px">
                    <button class="submit-btn" type="submit">
                        Reset Password
                    </button>
                </div>
                <div style="margin-top: 12px" class="login-text">
                    <p>Did you success to reset your password?</p>
                    <p>Please return to the application to log in.</p>
                </div>
            </form>
        </div>

        @endif

    </div>

    <script>
        let passwordShowBtn = document.getElementById('password_show_btn');
        let confirmPasswordShowBtn = document.getElementById('confirm_password_show_btn');
        let passwordInput = document.getElementById('password');
        let confirmPasswordInput = document.getElementById('confirm_password');

        passwordShowBtn.addEventListener('click', () => tooglePasswordShow(passwordShowBtn, passwordInput))
        confirmPasswordShowBtn.addEventListener('click', () => tooglePasswordShow(confirmPasswordShowBtn, confirmPasswordInput))

        const tooglePasswordShow = (button, input) => {
            button.firstElementChild.classList.toggle('display-none')
            button.lastElementChild.classList.toggle('display-none')
            
            if (button.firstElementChild.classList.contains('display-none')) {
                input.type = 'text'
            }else{
                input.type = 'password'
            }
        }
    </script>
</body>

</html>