import React, { useEffect, useState } from 'react';
import './VerseListing.css';
import tipsApi from '../../api/tipsApi';
import VerseRelatedStory from '../VerseRelatedStory/VerseRelatedStory';

const VerseListing = ({ verseText, onVerseClick }) => {
  const [verseData, setVerseData] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!verseText?.trim()) {
      // Clear data when search text is empty
      setVerseData(null);
      return;
    }

    const fetchData = async () => {
      setLoading(true);
      try {
        const result = await tipsApi.findVerse(verseText);
        setVerseData(result);
      } catch (error) {
        console.error('Error fetching verse data:', error);
        setVerseData(null);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [verseText]);

    useEffect(() => {
    if (
      Array.isArray(verseData) &&
      verseData.length === 1 &&
      verseData[0]?.term_slug
    ) {
      onVerseClick(verseData[0].term_slug);
    }
  }, [verseData, onVerseClick]);

  if (!verseText?.trim()) return null;

  if (loading) {
    return (
      <div className="container entry-content">
        <div className="spinner"></div>
      </div>
    );
  }

  if (verseData?.[0]?.error) {
    return <div className="find_ver_error">{verseData[0]?.error}</div>;
  }


  return (
    <div className="container book-container">
      {Array.isArray(verseData) &&
        verseData.map((chapter, index) => (
          <div key={index} className="book" id="find_verse">
            {chapter.verse_slug &&
              Object.entries(chapter.verse_slug).map(([verseRef, verseSlug]) => (
                <div key={verseRef} id={verseSlug} className="verses">
                  <span onClick={() => onVerseClick(verseSlug)}>{verseRef}</span>
                </div>
              ))}
          </div>
        ))}
    </div>
  );
};

export default VerseListing;
