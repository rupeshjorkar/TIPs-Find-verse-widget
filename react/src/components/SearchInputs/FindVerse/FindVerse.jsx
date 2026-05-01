import React, { useState } from 'react';

import './FindVerse.css';
import tipsApi from '../../../api/tipsApi';

const FindVerse = ({ onVerseData, onSearchVerse }) => {

    const [findVerseInput, setFindVerseInput] = useState('');

    const handleFindVerse = async () => {
        if (!findVerseInput.trim()) return;
        const result = await tipsApi.findVerse(findVerseInput);
        // onVerseData(result);
        onSearchVerse(findVerseInput);
        setFindVerseInput(''); 
    };

    const handleKeyPress = (e) => {
      if (e.key === 'Enter') {
        handleFindVerse();
      }
    };  

  return (
    <div className="find-verse-group">
      <input
        type="text"
        placeholder="Tips: Find verse"
        value={findVerseInput}
        onChange={(e) => setFindVerseInput(e.target.value)}
        onKeyDown={handleKeyPress}
      />
      <button 
        className="btn-find-verse" 
        onClick={handleFindVerse}
        // disabled={loading}
      >
        FIND VERSE
      </button>
    </div>
  );
};

export default FindVerse;