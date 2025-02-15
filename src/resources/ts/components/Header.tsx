import React, {useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import {Button} from '@/components/ui/button';
import {Music2} from 'lucide-react';

interface AuthState {
  isAuthenticated: boolean;
  spotifyId: string | null;
}

/**
 * ヘッダーコンポーネント
 * @constructor
 */
const Header = () => {
  const navigate = useNavigate();
  const [auth, setAuth] = useState<AuthState>({
    isAuthenticated: false,
    spotifyId: null
  });

  useEffect(() => {
    checkAuthStatus();
  }, []);

  const checkAuthStatus = async () => {
    try {
      const response = await fetch('/api/spotify/check-auth');
      const data = await response.json();
      setAuth({
        isAuthenticated: data.authenticated,
        spotifyId: data.spotify_id || null
      });
    } catch (error) {
      console.error('認証状態の確認に失敗しました:', error);
    }
  };

  const handleLogout = async () => {
    try {
      const response = await fetch('/api/spotify/logout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });

      if (!response.ok) throw new Error('ログアウトに失敗しました');

      setAuth({
        isAuthenticated: false,
        spotifyId: null
      });
      navigate('/');
    } catch (error) {
      console.error('ログアウトエラー:', error);
    }
  };

  return (
      <header className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <Music2 className="h-8 w-8 text-green-500"/>
              <span className="ml-2 text-xl font-bold">DJ tamayu</span>
            </div>
            <div>
              {auth.isAuthenticated ? (
                  <div className="flex items-center gap-4">
                    <span className="text-gray-600">ログイン中</span>
                    <Button
                        variant="outline"
                        onClick={handleLogout}
                    >
                      ログアウト
                    </Button>
                  </div>
              ) : (
                  <div className="flex items-center gap-4">
                    <span className="text-gray-600">ログインしていません</span>
                  </div>
              )}
            </div>
          </div>
        </div>
      </header>
  );
};

export default Header;