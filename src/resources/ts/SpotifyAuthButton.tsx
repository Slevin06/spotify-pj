import React, {Fragment} from "react";
import {AxiosError, AxiosResponse} from "axios";

// TODO: 型定義はあとで外だし
// interface SpotifyAuthUrlResponse {
//     spotifyAuthUrl: string;
// }

// interface SpotifyAuthUrlError extends AxiosError {
//     response?: AxiosResponse<any>;
// }

const SpotifyAuthButton = () => {

    // const [spotifyAuthUrl, setSpotifyAuthUrl] = useState('');

    // useEffect(() => {
    //     const fetchSpotifyAuthUrl = async () => {
    //         try {
    //             const response = await axios.get<SpotifyAuthUrlResponse>(GET_AUTH_URL_ENDPOINT);
    //             setSpotifyAuthUrl(response.data.spotifyAuthUrl);
    //
    //         } catch (error: unknown) {
    //             console.log(error);
    //         }
    //     };
    //     fetchSpotifyAuthUrl().then(r => console.log('success'));
    // }, []);

    const handleClick = () => {
        // window.location.href = '/api/to-spotify-login';
        window.location.href = '/to-spotify-login';
    };

    return (
        <Fragment>
            <div>
                <button
                    onClick={() => handleClick()}>Spotifyにログイン
                </button>
            </div>
        </Fragment>
    );
};

export default SpotifyAuthButton;
