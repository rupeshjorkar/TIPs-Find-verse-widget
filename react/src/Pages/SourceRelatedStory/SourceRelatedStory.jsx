import React, { useEffect, useState } from "react";
import './SourceRelatedStory.css';
import tipsApi from "../../api/tipsApi";
import Pagination from "../../components/Pagination/Pagination";

const SourceRelatedStory = ({ sourceSlug, onStoryClick }) => {
    const [loading, setLoading] = useState(true);
    const [sourceData, setSourceData] = useState(null);
    const [storyData, setStoryData] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    const fetchStory = async (page = 1) => {
        setLoading(true);
        try {
            const result = await tipsApi.fetchSourceRelatedStory(sourceSlug, page);
            setSourceData(result.sourceData || null);
            setStoryData(result.storyData || []);

            // Get pagination info from the response
            if (result.pagination && result.pagination.length > 0) {
                const totalPagesFromAPI = result.pagination[0].total_pages;
                setTotalPages(totalPagesFromAPI || 1);
            } else {
                setTotalPages(1);
            }
            setCurrentPage(page);

        } catch (error) {
            console.error("Error fetching source-related story:", error);
            setSourceData(null);
            setStoryData([]);
            setTotalPages(1);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (!sourceSlug) return;
        fetchStory(1);
    }, [sourceSlug]);

    const handlePageChange = (page) => {
        if (page !== currentPage) {
            fetchStory(page);
            // Scroll to top of the content when page changes
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    if (!sourceSlug) return null;
      // Show loading spinner
    if (loading) {
        return (
        <div className="entry-content container">
            <div className="spinner"></div>
        </div>
        );
    }

    return (
        <div className="entry-content container">
            <section className="book-stories-section">
                {sourceData && (
                    <div className="source_desc">
                        <h2>Source: {" "} {sourceData.title}</h2>
                        <p dangerouslySetInnerHTML={{ __html: sourceData.description }} />
                    </div>
                )}

                {storyData.length > 0 ? (
                    storyData.map((story) => (
                        <article key={story.id} className="book-story">
                            <div
                                onClick={() => onStoryClick(story.slug)}
                            >
                                <h2>
                                    {story?.title?.rendered}
                                    <span className="term with-original">{story?.title?.hover_title}</span>
                                </h2>
                                {story.geographical_link?.title && (
                                    <p className="tree-link">{story.geographical_link.title}</p>
                                )}
                            </div>
                            <div
                                className="story-content"
                                dangerouslySetInnerHTML={{ __html: story.content.rendered }}
                            />
                        </article>
                    ))
                ) : (
                    <p>No related stories found.</p>
                )}
                <Pagination
                    currentPage={currentPage}
                    totalPages={totalPages}
                    onPageChange={handlePageChange}
                />
            </section>
        </div>
    );
};

export default SourceRelatedStory;
