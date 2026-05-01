import React, { useEffect, useState } from "react";
import './VerseStoryDetail.css';
import tipsApi from "../../api/tipsApi";


const VerseStoryDetail = ({ storySlug, onVerseClick, onSourceClick }) => {
    const [storyDetail, setStoryDetail] = useState(null);
    const [loading, setLoading] = useState(true);
    const [currentSlug, setCurrentSlug] = useState(storySlug)


    const handleNextPrevious = (slug) =>{
        setCurrentSlug(slug);
    }

    useEffect(() => {
        if (!currentSlug) return;

        const fetchStoryDetail = async () => {
            setLoading(true);
            try {
                const result = await tipsApi.fetchStoryDetail(currentSlug);
                setStoryDetail(result);
            } catch (error) {
                console.error("Error fetching verse-related story:", error);
                setStoryDetail(null);
            } finally {
                setLoading(false);
            }
        };

        fetchStoryDetail();
    }, [currentSlug]);

      // Show loading spinner
  if (loading) {
    return (
      <div className="container">
        <div className="spinner"></div>
      </div>
    );
  }
    if (!storyDetail.length) return <p>No story found.</p>;

    return (
        <div className="container">
            <section className="book-stories-section story_detail">
                {storyDetail.map((item) => (
                    <>
                        <article key={item.id} className="book-story">
                            <h1>
                                {item.title?.rendered}
                                <span className="term with-original">{item.title?.hover_title}</span>
                            </h1>

                            {item.geographical_link?.title && (
                                <p className="tree-link">{item.geographical_link.title}</p>
                            )}

                            <div
                                className="entry-content"
                                dangerouslySetInnerHTML={{ __html: item.content?.rendered }}
                            />

                            <div className="entry-meta">

                                {/* LANGUAGES */}
                                {item?.taxonomies_list?.Language_slug &&
                                    Object.keys(item.taxonomies_list.Language_slug).length > 0 && (
                                        <p>
                                            <b>LANGUAGES:</b>&nbsp;
                                            {Object.keys(item.taxonomies_list.Language_slug).join('\u00A0\u00A0')}
                                        </p>
                                    )}

                                {/* VERSES */}
                                {item?.taxonomies_list?.Verse_slug &&
                                    Object.entries(item.taxonomies_list.Verse_slug).length > 0 && (
                                        <p>
                                            <b>VERSES:</b>&nbsp;
                                            {Object.entries(item.taxonomies_list.Verse_slug).map(([verse, verseSlug], i) => (
                                                <span 
                                                    key={i}
                                                    onClick={()=>onVerseClick(verseSlug)}
                                                >
                                                    {verse}&nbsp;&nbsp;
                                                </span>
                                            ))}
                                        </p>
                                    )}

                                {/* SOURCES */}
                                {item?.taxonomies_list?.Source_slug &&
                                    Object.entries(item.taxonomies_list.Source_slug).length > 0 && (
                                        <p>
                                            <b>SOURCES:</b>&nbsp;
                                            {Object.entries(item.taxonomies_list.Source_slug).map(([source, sourceSlug], i, arr) => (
                                                <span 
                                                    key={i}
                                                    onClick={()=>onSourceClick(sourceSlug)}
                                                >
                                                    {source}
                                                    {i !== arr.length - 1 && ' : '}
                                                </span>
                                            ))}
                                        </p>
                                    )}

                            </div>
                        </article>
                        <b>
                            <div className="prev-page">
                                <span aria-hidden="true" className="nav-subtitle"> Previous </span>
                                <a
                                    onClick={()=>handleNextPrevious(item?.prev_story?.slug)}
                                >
                                    <img decoding="async" src="https://tips.translation.bible/wp-content/plugins/tips-rest-api/images/back.png" alt="" />
                                    {item?.prev_story?.title}
                                </a>
                            </div>
                            <div className="next-page">
                                <span aria-hidden="true" className="nav-subtitle"> Next </span>
                                <a
                                    onClick={()=>handleNextPrevious(item?.next_story?.slug)}
                                >
                                    {item?.next_story?.title}
                                    <img decoding="async" src="https://tips.translation.bible/wp-content/plugins/tips-rest-api/images/next.png" alt="" />
                                </a>
                            </div>
                        </b>
                    </>
                ))}
            </section>
        </div>
    );
}

export default VerseStoryDetail;