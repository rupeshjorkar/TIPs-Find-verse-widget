import React, { useState, useEffect } from "react";
import './VerseRelatedStory.css';
import tipsApi from "../../api/tipsApi";
import Pagination from "../../components/Pagination/Pagination";

const VerseRelatedStory = ({ verseSlug, onStoryTitleClick, onTreeViewClick }) => {
const [currentSlug, setCurrentSlug] = useState(verseSlug);
  const [verseData, setVerseData] = useState(null); // for 'VerseData'
  const [verseRelatedStories, setVerseRelatedStories] = useState([]); // for "0", "1", ...
  const [loading, setLoading] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

    const fetchStory = async (currentSlug, page = 1) => {
      setLoading(true);
      try {
        const result = await tipsApi.fetchVerseRelatedStory(currentSlug, page);

        const verseInfo = result["VerseData"] || null;
       const paginationInfo = Array.isArray(result["pagination"]) ? result["pagination"] : [];
        

        const stories = Object.entries(result)
          .filter(([key]) => key !== "VerseData")
          .map(([_, value]) => value);

        setVerseData(verseInfo);
        setVerseRelatedStories(stories);
        setCurrentPage(Number(paginationInfo[0]?.page) || 1);       
        setTotalPages(Number(paginationInfo[0]?.total_pages) || 1);   
      } catch (error) {
        console.error("Error fetching verse-related story:", error);
        setVerseData(null);
        setVerseRelatedStories([]);
      } finally {
        setLoading(false);
      }
    };

    useEffect(() => {
        if (verseSlug !== currentSlug) {
        setCurrentSlug(verseSlug);
        }
    }, [verseSlug]);

  // Fetch data when currentSlug changes
  useEffect(() => {
    if (!currentSlug) return;
    fetchStory(currentSlug, 1);
  }, [currentSlug]);

    // Handle page change
  const handlePageChange = (newPage) => {
    setCurrentPage(newPage);
    fetchStory(currentSlug, newPage);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // Handle previous/next navigation
  const handlePrevious = (slug) => {
    if (slug) setCurrentSlug(slug);
  };

  const handleNext = (slug) => {
    if (slug) setCurrentSlug(slug);
  };

  if (!currentSlug) return null;
    // Show loading spinner
  if (loading) {
    return (
      <div className="container entry-content inner-page-design">
        <div className="spinner"></div>
      </div>
    );
  }

  return (
    <div className="container entry-content inner-page-design">

      {/* Render VerseData */}
      {verseData && (
        <>
            <div className="page_main_title">
                <h1 className="page_main_title">Verse: {verseData.title}</h1>
            </div>
            <div className="verse-text-original">
                <p>{verseData.verse_gree}</p>
            </div>
            <div className="verse-text-original">
                <p dangerouslySetInnerHTML={{ __html: verseData.verse_english || '' }}/>
            </div>
            <div className="next-previous">
                <span
                    className="previous"
                    onClick={()=>handlePrevious(verseData.previous_verse)}
                >{verseData.previous_verse_name}</span>
                <span className="seprater"></span>
                <span 
                    className="next"
                    onClick={()=>handleNext(verseData.next_verse)}
                >{verseData.next_verse_name}</span>
            </div>
        </>
      )}

      {/* Render Related VerseRealtedStories */}
      <section className="book-stories-section">
      <article className="book-story"></article>
      {verseRelatedStories.length === 0 ? (
        <p>No related stories found.</p>
      ) : (
        verseRelatedStories.map((item, id) => (
            <article key={id}  className="book-story">
                <h2
                    onClick={() => onStoryTitleClick(item.slug)}
                >
                    {item.title?.rendered}
                    <span className="term with-original" data-original={item.title?.hover_title}>{item.title?.hover_title}</span>
                </h2>
                {item.geographical_link?.title && (
                  <p 
                    className="tree-link"
                    onClick={()=>onTreeViewClick(item.geographical_link?.link)}
                  >{item.geographical_link.title}</p>
                )}
                <div className="entry-content" dangerouslySetInnerHTML={{ __html: item.content?.rendered }}/>
                <div className="language-content"></div>
            </article>
        ))
      )}
      {totalPages > 1 && (
          <Pagination
            currentPage={currentPage}
            totalPages={totalPages}
            onPageChange={handlePageChange}
          />
        )}
      </section>
    </div>
  );
};

export default VerseRelatedStory;
