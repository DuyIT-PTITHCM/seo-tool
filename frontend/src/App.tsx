import React, { useEffect, useState } from 'react';
import './App.css';
import Pusher from 'pusher-js';
import { Input, Button, Table, Spin } from 'antd';
const { TextArea } = Input;

function App() {
  const [url, setUrl] = useState('');
  const [keywords, setKeywords] = useState('');
  const [rankings, setRankings] = useState([]);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const pusherKey = '5a8157b43b059b0a1db6';
    const pusherCluster = 'ap1';

    const pusher = new Pusher(pusherKey, {
      cluster: pusherCluster,
    });

    const channel = pusher.subscribe('my-channel');
    channel.bind('ranking-complete', (data: any) => {
      if (data) {
        setRankings(data.data);
        setIsLoading(false);
      }
    });

  }, []);

  const handleUrlChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setUrl(event.target.value);
  };

  const handleKeywordsChange = (event: React.ChangeEvent<HTMLTextAreaElement>) => {
    setKeywords(event.target.value);
  };

  const handleSearch = () => {
    setIsLoading(true);

    const keywordArray = keywords;

    // Call API with url and keywords
    const params : any = {
      url,
      keywords: keywordArray,
    };

    // Convert params object to query string
    const queryString = Object.keys(params).map(key => {
      if (Array.isArray(params[key])) {
        // Convert array values to a comma-separated string
        return `${encodeURIComponent(key)}=${encodeURIComponent(params[key].join(','))}`;
      } else {
        return `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`;
      }
    }).join('&');

    // Perform API call here using your preferred method (e.g., fetch, axios)
    fetch(`api/v1.0/ranking/get?${queryString}`)
      .then(response => response.json())
      .then(data => {
        // Handle API response
        console.log(data);
        setIsLoading(true);
      })
      .catch(error => {
        // Handle error
        console.error(error);
        setIsLoading(false);
      });
  };

  const columns = [
    {
      title: 'Keywords',
      dataIndex: 'keyword',
      key: 'keyword',
    },
    {
      title: 'Google Rank',
      dataIndex: 'googleRank',
      key: 'googleRank',
    },
    {
      title: 'Google Search Results',
      dataIndex: 'googleLink',
      key: 'googleLink',
    },
    {
      title: 'Yahoo Rank',
      dataIndex: 'yahooRank',
      key: 'yahooRank',
    },
    {
      title: 'Yahoo Search Results',
      dataIndex: 'yahooLink',
      key: 'yahooLink',
    },
  ];

  return (
    <div className="container">
      <div className="header">
        <h1>SEO TOOL</h1>
      </div>
      <div className="content">
        <div className="form">
          <label htmlFor="url">URL:</label>
          <Input type="text" id="url" value={url} onChange={handleUrlChange} />
          <label htmlFor="keywords">Keywords:</label>
          <TextArea id="keywords" value={keywords} onChange={handleKeywordsChange} rows={5} />
          <Button type="primary" onClick={handleSearch}>Search</Button>
        </div>
        <div className="table">
          {isLoading ? (
            <div className="loading">
              <Spin size="large" />
              <p>Loading...</p>
            </div>
          ) : (
            <Table columns={columns} dataSource={rankings} />
          )}
        </div>
      </div>
      <div className="footer">
        <p>© 2023 SEO TOOL. All rights reserved.</p>
      </div>
    </div>
  );
}

export default App;
