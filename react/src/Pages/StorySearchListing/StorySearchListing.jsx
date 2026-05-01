import React, { useState, useEffect } from 'react';
import './StorySearchListing.css';
import Pagination from '../../components/Pagination/Pagination';
import tipsApi from '../../api/tipsApi';

const StorySearchListing = ({
  searchText,
  onStoryClick,
}) => {
  const [searchTextData, setSearchTextData] = useState(null);
  const [loading, setLoading] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [currentSearchTerm, setCurrentSearchTerm] = useState('');

  // Function to fetch data from API
  const fetchSearchData = async (searchTerm, page = 1) => {
    if (!searchTerm?.trim()) return;

    setLoading(true);

    try {
      const result = await tipsApi.fetchSearchStory(searchTerm, page);
      setSearchTextData(result);
      // Extract pagination info
      const total = result?.pagination?.[0]?.total_pages || 1;
      setTotalPages(total);
      console.log('Total pages:', total);

    } catch (error) {
      console.error('Error fetching search data:', error);
      setSearchTextData(null);
    } finally {
      setLoading(false);
    }
  };

  // Effect to handle new search text from parent
  useEffect(() => {
    if (!searchText?.trim()) {
      // Clear data when search text is empty
      setSearchTextData(null);
      setCurrentSearchTerm('');
      setTotalPages(1);
      setCurrentPage(1);
      return;
    }

    if (searchText !== currentSearchTerm) {
      setCurrentSearchTerm(searchText);
      setCurrentPage(1);
      fetchSearchData(searchText, 1);
    }
  }, [searchText, currentSearchTerm]);


  // Handle page change
  const handlePageChange = (newPage) => {
    setCurrentPage(newPage);
    fetchSearchData(currentSearchTerm, newPage);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // Don't render anything if no search has been performed
  if (!searchTextData && !loading) return null;

  // Show loading spinner
  if (loading) {
    return (
      <div className="container entry-content">
        <div className="spinner"></div>
      </div>
    );
  }

  // Handle error case
  if (searchTextData && searchTextData[0]?.error) {
    return <div className="find_ver_error">{searchTextData[0]?.error}</div>;
  }

  const storyData = searchTextData?.storyData;

  return (
    <div className="container entry-content">
      <section className="book-stories-section">
        {storyData?.map((story) => (
          <article
            key={story.id}
            className="book-story"
          >
            <div onClick={() => onStoryClick(story.slug)}>
              <h2 className="sss">
                {story?.title?.rendered}
                {story?.title?.hover_title && (
                  <span className="term with-original">{story?.title?.hover_title}</span>
                )}
              </h2>
            </div>
            <div
              className="entry-content"
              dangerouslySetInnerHTML={{ __html: story.content.rendered }}
            />
          </article>
        ))}

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

export default StorySearchListing;