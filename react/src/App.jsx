import React, { useState } from 'react';
import FindVerse from './components/SearchInputs/FindVerse/FindVerse';
import PickCategory from './components/SearchInputs/PickCategory/PickCategory';
import './App.css';
import VerseListing from './Pages/VerseListing/VerseListing';
import VerseRelatedStory from './Pages/VerseRelatedStory/VerseRelatedStory';
import VerseStoryDetail from './Pages/VerseStoryDetail/VerseStoryDetail';
import SearchStory from './components/SearchInputs/SearchStory/SearchStory';
import StorySearchListing from './Pages/StorySearchListing/StorySearchListing';
import SourceRelatedStory from './Pages/SourceRelatedStory/SourceRelatedStory';
import CategoryRelatedStory from './Pages/CategoryRelatedStory/CategoryRelatedStory';
import TreeView from './Pages/TreeView/TreeView';

function App() {
  const [verseList, setVerseList] = useState(null);
  const [verseSlug, setVerseSlug] = useState(null);
  const [sourceSlug, setSourceSlug] = useState(null);
  const [storySlug, setStorySlug] = useState(null);
  const [searchText, setSearchText] = useState('');
  const [categorySlug, setCategorySlug] = useState(null);
  const [loading, setLoading] = useState(false);
  const [termId, setTermId] = useState(null);

  const handleVerseList = (text) => {
    setVerseList(text);
    setVerseSlug('');
    setStorySlug('');
    setSearchText(''); 
    setSourceSlug('');
    setCategorySlug('');
    setTermId('');
  };

  const handleVerseSlug = (slug) => {
    setVerseSlug(slug);
    setVerseList('');
    setStorySlug('');
    setSearchText(''); 
    setSourceSlug('');
    setCategorySlug('');
    setTermId('');
  };

  const handleStoryTitleClick = (slug) => {
    setStorySlug(slug);
    setVerseSlug('');
    setVerseList('');
    setSearchText('');
    setSourceSlug('');
    setCategorySlug('');
    setTermId('');
  };

  const handleSearchText = (text) => {
    setSearchText(text);
    setVerseSlug('');
    setVerseList('');
    setStorySlug('');
    setSourceSlug('');
    setCategorySlug('');
    setTermId('');
  };

  const handleSourceSlug = (slug) => {
    setSourceSlug(slug);
    setSearchText(''); 
    setVerseSlug('');
    setVerseList('');
    setStorySlug('');
    setCategorySlug('');
    setTermId('');
  };

  const handleCategorySlug = (slug) => {
    setCategorySlug(slug);
    setSourceSlug('');
    setSearchText(''); 
    setVerseSlug('');
    setVerseList('');
    setStorySlug('');
    setTermId('');
  }

  const handleTermId = (termId) =>{
    setTermId(termId)
    setCategorySlug('');
    setSourceSlug('');
    setSearchText(''); 
    setVerseSlug('');
    setVerseList('');
    setStorySlug('');
  }

  // Handle loading state changes from StorySearchListing
  const handleLoadingChange = (isLoading) => {
    setLoading(isLoading);
  };

  return (
    <>
      <div className='appcontainer'>
        <FindVerse
          // onVerseData={handleVerseList}
          onSearchVerse={handleVerseList}
          loading={loading} 
          setLoading={setLoading}
        />
        <SearchStory 
          onSearchText={handleSearchText}
          loading={loading}
        />
        <PickCategory 
          categorySlug={handleCategorySlug}
        />
      </div>

      <div>
        {loading ? (
          <div className="spinner"></div>
        ) : (
          <>
            <VerseListing
              verseText={verseList}
              onVerseClick={handleVerseSlug}
            />
            {verseSlug && (
              <VerseRelatedStory
                verseSlug={verseSlug}
                onStoryTitleClick={handleStoryTitleClick}
                onTreeViewClick={handleTermId}
              />
            )}
            {sourceSlug && (
              <SourceRelatedStory
                sourceSlug={sourceSlug}
                onStoryClick={handleStoryTitleClick}
              />
            )}
            {storySlug && (
              <VerseStoryDetail
                storySlug={storySlug}
                onVerseClick={handleVerseSlug}
                onSourceClick={handleSourceSlug}
              />
            )}
            {searchText && (
            <StorySearchListing
              searchText={searchText}
              onStoryClick={handleStoryTitleClick}
              onLoadingChange={handleLoadingChange}
            />
            )}
            {categorySlug && (
              <CategoryRelatedStory
                categorySlug={categorySlug}
                onStoryClick={handleStoryTitleClick}
              />
            )}
            {termId && (
              <TreeView termId={termId}/>
            )}
          </>
        )}
      </div>
    </>
  );
}

export default App;