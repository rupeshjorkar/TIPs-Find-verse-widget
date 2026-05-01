import React, { useState } from 'react';
import './SearchStory.css';

const SearchStory = ({ onSearchText, loading }) => {
  const [findSearchInput, setFindSearchInput] = useState('');

  const handleFindText = () => {
    if (!findSearchInput.trim()) return;
    onSearchText(findSearchInput);
    setFindSearchInput('');
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter') {
      handleFindText();
    }
  };

  return (
    <div className="search-text-group">
      <input
        type="text"
        placeholder="Search"
        value={findSearchInput}
        onChange={(e) => setFindSearchInput(e.target.value)}
        onKeyDown={handleKeyPress}
      />
      <button 
        className="btn-search-text" 
        onClick={handleFindText} 
        disabled={loading}
      >
        {loading ? 'SEARCHING...' : 'SEARCH TEXT'}
      </button>
    </div>
  );
};

export default SearchStory;