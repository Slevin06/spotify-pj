import {useState, useEffect} from 'react';
import axios from 'axios';

interface UseSpotifyAuthReturn {
  isAuthenticated: boolean;
  isLoading: boolean;
}

export const useSpotifyAuth = (): UseSpotifyAuthReturn => {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const response = await axios.get('/api/auth/check');
        setIsAuthenticated(response.data.authenticated);
      } catch (error) {
        console.error('Auth check failed:', error);
      } finally {
        setIsLoading(false);
      }
    };
    checkAuth();
  }, []);

  return {isAuthenticated, isLoading};
};