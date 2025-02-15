import React from "react";
import {useSpotifyAuth} from "@/hooks/useSpotifyAuth";
import {Loader} from 'lucide-react';
import {Button} from "@/components/ui/button";

/**
 * Spotify認証コンポーネント
 * @constructor
 */
const SpotifyAuth = () => {
  const {isAuthenticated, isLoading} = useSpotifyAuth();

  const handleLogin = () => {
    window.location.href = '/api/to-spotify-login';
  };

  if (isLoading) {
    return <Loader className="animate-spin mr-2"/>;
  }

  if (isAuthenticated) {
    window.location.href = '/form';
    return null;
  }

  return (
      <div
          className="flex flex-col items-center justify-center min-h-screen p-4">
        <h1 className="text-3xl font-bold mb-8">DJ tamanoyu</h1>
        <Button
            size="lg"
            className="bg-green-500 hover:bg-green-600"
            onClick={handleLogin}
        >
          Spotifyログイン
        </Button>
      </div>
  );
};

export default SpotifyAuth;