import React from 'react';
import './Pagination.css';

const Pagination = ({ 
  currentPage, 
  totalPages, 
  onPageChange 
}) => {
  // Only hide pagination if we explicitly have 1 total page
  if (totalPages <= 1) return null;

  // Function to generate page numbers with ellipsis
  const getPageNumbers = () => {
    if (totalPages <= 3) {
      return Array.from({ length: totalPages }, (_, i) => i + 1);
    }
    const pages = [];
    
    // Always show first page
    pages.push(1);
    
    if (currentPage > 4) {
      // Add ellipsis if current page is far from start (changed from 3 to 4)
      pages.push('...');
    }
    
    // Pages around current page
    const startPage = Math.max(2, currentPage - 1);
    const endPage = Math.min(totalPages - 1, currentPage + 1);
    
    // Ensure we show at least pages 2 and 3 when on page 1
    const actualStartPage = currentPage <= 2 ? 2 : startPage;
    const actualEndPage = currentPage <= 2 ? Math.min(3, totalPages - 1) : endPage;
    
    for (let i = actualStartPage; i <= actualEndPage; i++) {
      if (i !== 1 && i !== totalPages) {
        pages.push(i);
      }
    }
    
    if (currentPage < totalPages - 3) {
      // Add ellipsis if current page is far from end (changed from 2 to 3)
      pages.push('...');
    }
    
    // Always show last page if more than 1 page
    if (totalPages > 1) {
      pages.push(totalPages);
    }
    
    return pages;
  };

  const handlePageClick = (page) => {
    if (typeof page === 'number' && page !== currentPage && page >= 1 && page <= totalPages) {
      onPageChange(page);
    }
  };

  return (
    <div className="pagination">
      {/* Previous button */}
      {currentPage > 1 && (
        <a
          className="prev page-numbers no-lightbox"
          onClick={(e) => {
            e.preventDefault();
            handlePageClick(currentPage - 1);
          }}
          href="#"
          aria-label="Previous page"
        >
          <img
            decoding="async"
            src="https://tips.translation.bible/wp-content/plugins/tips-rest-api/images/prev_icon.png"
            alt="Previous"
          />
        </a>
      )}
      
      {/* Page numbers */}
      {getPageNumbers().map((page, index) => (
        <React.Fragment key={index}>
          {page === '...' ? (
            <span className="page-numbers dots">…</span>
          ) : (
            currentPage === page ? (
              <span
                aria-current="page"
                className="page-numbers current"
              >
                {page}
              </span>
            ) : (
              <a
                className="page-numbers"
                onClick={(e) => {
                  e.preventDefault();
                  handlePageClick(page);
                }}
                href="#"
              >
                {page}
              </a>
            )
          )}
        </React.Fragment>
      ))}
      
      {/* Next button */}
      {currentPage < totalPages && (
        <a
          className="next page-numbers no-lightbox"
          onClick={(e) => {
            e.preventDefault();
            handlePageClick(currentPage + 1);
          }}
          href="#"
          aria-label="Next page"
        >
          <img
            decoding="async"
            src="https://tips.translation.bible/wp-content/plugins/tips-rest-api/images/prev_icon.png"
            alt="Next"
            style={{ transform: 'rotate(180deg)' }}
          />
        </a>
      )}
    </div>
  );
};

export default Pagination;