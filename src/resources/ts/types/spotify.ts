interface SpotifyAuthUser {
  id: string;
  email: string;
  token: string;
  refreshToken: string;
  expiresIn: number;
}