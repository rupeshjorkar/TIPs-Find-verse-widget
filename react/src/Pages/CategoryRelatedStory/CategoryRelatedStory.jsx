import React, { useEffect, useState } from "react";
import "./CategoryRelatedStory.css";
import tipsApi from "../../api/tipsApi";
import Pagination from "../../components/Pagination/Pagination";

const CategoryRelatedStory = ({ categorySlug, onStoryClick }) => {
  const [categoryData, setCategoryData] = useState(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(false);
  // Function to fetch data from API
  const fetchCategoryData = async (categorySlug, page = 1) => {
    if (!categorySlug?.trim()) return;

    setLoading(true);

    try {
      const result = await tipsApi.fetchCategoryRelatedStory(
        categorySlug,
        page
      );
      setCategoryData(result);
      // Extract pagination info
      const total = result?.pagination?.[0]?.total_pages || 1;
      setTotalPages(total);
    } catch (error) {
      setCategoryData(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (categorySlug) {
      setCurrentPage(1);
      fetchCategoryData(categorySlug, 1);
    }
  }, [categorySlug]);

  // Handle page change
  const handlePageChange = (newPage) => {
    setCurrentPage(newPage);
    fetchCategoryData(categorySlug, newPage);
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  // Don't render anything if no search has been performed
  if (!categoryData && !loading) return null;

  // Show loading spinner
  if (loading) {
    return (
      <div className="container entry-content">
        <div className="spinner"></div>
      </div>
    );
  }

  // Convert object to array before rendering
const categoryArray = Array.isArray(categoryData)
  ? categoryData
  : Object.values(categoryData).filter(item => typeof item === "object" && item.id);

  return (
    <div className="container entry-content">
      <section className="book-stories-section">
        {categoryArray?.map((story) => (
          <article key={story.id} className="book-story">
            <div onClick={() => onStoryClick(story.slug)}>
              <h2 className="sss">
                {story?.title?.rendered}
                {story?.title?.hover_title && (
                  <span className="term with-original">
                    {story?.title?.hover_title}
                  </span>
                )}
              </h2>
              {story.geographical_link?.title && (
                <p className="tree-link">{story.geographical_link.title}</p>
              )}
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

export default CategoryRelatedStory;
