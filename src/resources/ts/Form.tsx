import React, {useEffect, useState} from 'react';
import {Alert, AlertTitle, AlertDescription} from '@/components/ui/alert';
import {Loader} from 'lucide-react';
import {useNavigate} from "react-router-dom";

interface Artist {
  id: string;
  name: string;
}

interface FormState {
  artists: Artist[];
  searchQuery: string;
  searchResults: Artist[];
  isLoading: boolean;
  message: string | null;
  error: string | null;
}

const MAX_ARTISTS = 5;

/**
 * プレイリスト作成フォームコンポーネント
 * @constructor
 */
const Form = () => {
  const navigate = useNavigate();
  const [formState, setFormState] = useState<FormState>({
    artists: [],
    searchQuery: '',
    searchResults: [],
    isLoading: false,
    message: null,
    error: null
  });

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const response = await fetch('/api/spotify/check-auth');
        const data = await response.json();

        if (!data.authenticated) {
          navigate('/');  // 未認証の場合はトップページへリダイレクト
        }
      } catch (error) {
        console.error('認証チェックエラー:', error);
        navigate('/');
      }
    };

    checkAuth();
  }, [navigate]);

  const searchArtists = async () => {
    if (!formState.searchQuery.trim()) return;

    setFormState(prev => ({...prev, isLoading: true, error: null}));

    try {
      const response = await fetch(`/api/spotify/search-artists?query=${encodeURIComponent(formState.searchQuery)}`, {
        headers: {
          'Accept': 'application/json'
        }
      });

      if (!response.ok) throw new Error('検索に失敗しました');

      const data = await response.json();
      setFormState(prev => ({
        ...prev,
        searchResults: data.artists.items.map((artist: any) => ({
          id: artist.id,
          name: artist.name
        }))
      }));
    } catch (error) {
      setFormState(prev => ({
        ...prev,
        error: error instanceof Error ? error.message : '予期せぬエラーが発生しました'
      }));
    } finally {
      setFormState(prev => ({...prev, isLoading: false}));
    }
  };

  const handleSearchInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormState(prev => ({...prev, searchQuery: e.target.value}));
  };

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    searchArtists();
  };

  const addArtist = (artist: Artist) => {
    if (formState.artists.length >= MAX_ARTISTS) {
      setFormState(prev => ({
        ...prev,
        error: `アーティストは最大${MAX_ARTISTS}人までです`
      }));
      return;
    }

    if (formState.artists.some(a => a.id === artist.id)) {
      setFormState(prev => ({
        ...prev,
        error: 'このアーティストは既に追加されています'
      }));
      return;
    }

    setFormState(prev => ({
      ...prev,
      artists: [...prev.artists, artist],
      searchQuery: '',
      searchResults: [],
      error: null
    }));
  };

  const removeArtist = (artistId: string) => {
    setFormState(prev => ({
      ...prev,
      artists: prev.artists.filter(a => a.id !== artistId)
    }));
  };

  const handleCreatePlaylist = async () => {
    if (formState.artists.length === 0) {
      setFormState(prev => ({
        ...prev,
        error: 'アーティストを選択してください'
      }));
      return;
    }

    setFormState(prev => ({...prev, isLoading: true, error: null}));
    try {
      const response = await fetch('/api/spotify/create-playlist', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          artist_ids: formState.artists.map(a => a.id)
        })
      });

      if (!response.ok) throw new Error('プレイリストの作成に失敗しました');

      const result = await response.json();
      setFormState(prev => ({
        ...prev,
        message: 'プレイリストを作成しました！',
        artists: [],
        searchQuery: '',
        searchResults: []
      }));
    } catch (error) {
      setFormState(prev => ({
        ...prev,
        error: error instanceof Error ? error.message : '予期せぬエラーが発生しました'
      }));
    } finally {
      setFormState(prev => ({...prev, isLoading: false}));
    }
  };

  return (
      <div className="max-w-2xl mx-auto p-6">
        <h1 className="text-2xl font-bold mb-6">プレイリスト作成</h1>

        {formState.message && (
            <Alert variant="success" className="mb-4">
              <AlertTitle>成功</AlertTitle>
              <AlertDescription>{formState.message}</AlertDescription>
            </Alert>
        )}

        {formState.error && (
            <Alert variant="destructive" className="mb-4">
              <AlertTitle>エラー</AlertTitle>
              <AlertDescription>{formState.error}</AlertDescription>
            </Alert>
        )}

        <div className="mb-6">
          <h2 className="text-lg font-semibold mb-2">選択したアーティスト</h2>
          {formState.artists.length === 0 ? (
              <p className="text-gray-500">アーティストを追加してください（最大{MAX_ARTISTS}人）</p>
          ) : (
              <ul className="space-y-2">
                {formState.artists.map(artist => (
                    <li key={artist.id}
                        className="flex items-center justify-between p-2 bg-gray-50 rounded">
                      <span>{artist.name}</span>
                      <button
                          onClick={() => removeArtist(artist.id)}
                          className="text-red-500 hover:text-red-700"
                      >
                        削除
                      </button>
                    </li>
                ))}
              </ul>
          )}
        </div>

        <form onSubmit={handleSearchSubmit} className="mb-6">
          <div className="flex gap-2">
            <input
                type="text"
                value={formState.searchQuery}
                onChange={handleSearchInputChange}
                placeholder="アーティスト名を入力"
                className="flex-1 p-2 border rounded"
            />
            <button
                type="submit"
                disabled={formState.isLoading}
                className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:bg-blue-300"
            >
              検索
            </button>
          </div>
        </form>

        {formState.searchResults.length > 0 && (
            <div className="mb-6">
              <h2 className="text-lg font-semibold mb-2">検索結果</h2>
              <ul className="space-y-2">
                {formState.searchResults.map(artist => (
                    <li key={artist.id}
                        className="flex items-center justify-between p-2 bg-gray-50 rounded">
                      <span>{artist.name}</span>
                      <button
                          onClick={() => addArtist(artist)}
                          className="text-blue-500 hover:text-blue-700"
                      >
                        追加
                      </button>
                    </li>
                ))}
              </ul>
            </div>
        )}

        <button
            onClick={handleCreatePlaylist}
            disabled={formState.isLoading || formState.artists.length === 0}
            className="w-full p-3 bg-green-500 text-white rounded hover:bg-green-600 disabled:bg-green-300 flex items-center justify-center"
        >
          {formState.isLoading ? (
              <>
                <Loader className="animate-spin mr-2"/>
                処理中...
              </>
          ) : (
              'プレイリストを作成'
          )}
        </button>
      </div>
  );
};

export default Form;