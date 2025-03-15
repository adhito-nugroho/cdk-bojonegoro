// Utility Functions
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Error Handler
const handleError = (error) => {
  console.error("An error occurred:", error);
  const errorMessage = document.createElement("div");
  errorMessage.className = "alert alert-danger";
  errorMessage.textContent = "Terjadi kesalahan. Silakan coba lagi nanti.";
  document.body.appendChild(errorMessage);
  setTimeout(() => errorMessage.remove(), 5000);
};

// Initialize AOS
AOS.init({
  duration: 800,
  easing: 'ease-in-out',
  once: true,
  mirror: false
});

// Navbar Scroll Effect
const navbar = document.querySelector(".navbar");
const handleScroll = debounce(() => {
  if (window.scrollY > 50) {
    navbar.classList.add("navbar-scrolled");
  } else {
    navbar.classList.remove("navbar-scrolled");
  }
}, 100);

window.addEventListener("scroll", handleScroll);

// Initialize Map
document.addEventListener("DOMContentLoaded", function () {
  try {
    var map = L.map("map").setView([-7.1507, 111.8871], 8);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "© OpenStreetMap contributors",
    }).addTo(map);

    const locations = [
      { name: "Bojonegoro", coords: [-7.1507, 111.8871] },
      { name: "Tuban", coords: [-6.8989, 112.0531] },
      { name: "Lamongan", coords: [-7.1089, 112.4168] },
      { name: "Gresik", coords: [-7.1666, 112.655] },
      { name: "Sidoarjo", coords: [-7.4558, 112.7183] },
      { name: "Surabaya", coords: [-7.2575, 112.7521] },
    ];

    locations.forEach(function (loc) {
      L.marker(loc.coords).addTo(map).bindPopup(loc.name);
    });
  } catch (error) {
    handleError(error);
  }
});

// Contact Form Handler
const contactForm = document.querySelector(".contact-form");
if (contactForm) {
  contactForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
      // Show loading state
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
      submitBtn.disabled = true;

      // Get form data
      const formData = {
        nama: document.getElementById("nama").value,
        email: document.getElementById("email").value,
        telepon: document.getElementById("telepon").value,
        kategori: document.getElementById("kategori").value,
        pesan: document.getElementById("pesan").value,
      };

      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 1500));

      // Reset form and show success
      contactForm.reset();
      const alert = document.createElement("div");
      alert.className = "alert alert-success mt-3";
      alert.innerHTML =
        "Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.";
      contactForm.appendChild(alert);
      setTimeout(() => alert.remove(), 5000);
    } catch (error) {
      handleError(error);
    } finally {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  });
}

// Forest Statistics Chart
const forestStats = document.getElementById("forestStats");
if (forestStats) {
  try {
    new Chart(forestStats, {
      type: "bar",
      data: {
        labels: [
          "Hutan Produksi",
          "Hutan Rakyat",
          "Area Rehabilitasi",
          "Perhutanan Sosial",
        ],
        datasets: [
          {
            label: "Luas Area (Ha)",
            data: [45000, 15000, 5000, 10000],
            backgroundColor: [
              "rgba(46, 125, 50, 0.8)",
              "rgba(76, 175, 80, 0.8)",
              "rgba(129, 199, 132, 0.8)",
              "rgba(27, 94, 32, 0.8)",
            ],
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return value.toLocaleString() + " Ha";
              },
            },
          },
        },
      },
    });
  } catch (error) {
    handleError(error);
  }
}

// Progress Bar Animation with Intersection Observer
const animateProgress = () => {
  document.querySelectorAll(".progress-bar").forEach((bar) => {
    const width = bar.style.width;
    bar.style.width = "0";
    requestAnimationFrame(() => {
      bar.style.width = width;
    });
  });
};

const progressObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateProgress();
        progressObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.5 }
);

document
  .querySelectorAll(".progress-list")
  .forEach((el) => progressObserver.observe(el));

// Back to Top Button
const backToTopButton = document.getElementById("backToTop");
window.addEventListener(
  "scroll",
  debounce(() => {
    if (window.scrollY > 300) {
      backToTopButton.classList.add("show");
    } else {
      backToTopButton.classList.remove("show");
    }
  }, 100)
);

backToTopButton.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
});

// Swiper Initialization
document.addEventListener("DOMContentLoaded", function () {
  try {
    const swipers = document.querySelectorAll(".swiper-container");
    swipers.forEach((swiperElement) => {
      new Swiper(swiperElement, {
        loop: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
      });
    });
  } catch (error) {
    handleError(error);
  }
});

// Image Preview Modal
function initializeImagePreviews() {
  const galleryThumbnails = document.querySelectorAll(".gallery-thumbnail");

  galleryThumbnails.forEach((thumbnail) => {
    thumbnail.addEventListener("click", function () {
      try {
        const imageSrc = this.getAttribute("data-image");
        const modal = document.createElement("div");
        modal.className = "modal-preview";
        modal.innerHTML = `
                  <span class="modal-close">&times;</span>
                  <img src="${imageSrc}" alt="Preview">
              `;

        document.body.appendChild(modal);
        document.body.style.overflow = "hidden";

        const closeModal = () => {
          modal.remove();
          document.body.style.overflow = "auto";
        };

        modal
          .querySelector(".modal-close")
          .addEventListener("click", closeModal);
        modal.addEventListener("click", (e) => {
          if (e.target === modal) closeModal();
        });

        const handleKeyDown = (e) => {
          if (e.key === "Escape") {
            closeModal();
            document.removeEventListener("keydown", handleKeyDown);
          }
        };

        document.addEventListener("keydown", handleKeyDown);
      } catch (error) {
        handleError(error);
      }
    });
  });
}

// Smooth Scroll dengan offset yang tepat
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;

    const targetElement = document.querySelector(targetId);
    if (targetElement) {
      const headerOffset = 80; // Sesuaikan dengan tinggi navbar
      const elementPosition = targetElement.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }
  });
});

// Lazy Loading Images
const lazyLoadImages = () => {
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.remove("lazy");
        observer.unobserve(img);
      }
    });
  });

  document.querySelectorAll("img[data-src]").forEach((img) => {
    imageObserver.observe(img);
  });
};

// Initialize all functions when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  try {
    // Existing initializations
    initializeImagePreviews();
    lazyLoadImages();
    initCounters();
    
    // Initialize charts - dengan pengecekan
    const forestAreaChart = document.getElementById('forestAreaChart');
    const forestProductionChart = document.getElementById('forestProductionChart');
    
    // Destroy existing charts if they exist
    if (window.forestAreaChartInstance) {
      window.forestAreaChartInstance.destroy();
    }
    if (window.forestProductionChartInstance) {
      window.forestProductionChartInstance.destroy();
    }
    
    if (forestAreaChart) {
      window.forestAreaChartInstance = new Chart(forestAreaChart, {
        type: 'doughnut',
        data: {
          labels: ['Hutan Produksi', 'Hutan Lindung', 'Hutan Rakyat', 'Hutan Kota'],
          datasets: [{
            data: [45000, 25000, 15000, 5000],
            backgroundColor: [
              'rgba(46, 125, 50, 0.8)',
              'rgba(56, 142, 60, 0.8)',
              'rgba(76, 175, 80, 0.8)',
              'rgba(129, 199, 132, 0.8)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    }

    if (forestProductionChart) {
      window.forestProductionChartInstance = new Chart(forestProductionChart, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
          datasets: [{
            label: 'Produksi (Ton)',
            data: [1200, 1900, 1500, 1800, 2200, 1600],
            backgroundColor: 'rgba(46, 125, 50, 0.8)',
            borderColor: 'rgba(46, 125, 50, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  } catch (error) {
    console.error('Initialization error:', error);
  }
});

// Cleanup function
window.addEventListener("beforeunload", () => {
  // Cleanup event listeners to prevent memory leaks
  window.removeEventListener("scroll", handleScroll);
});

// Gallery Functionality
const initGallery = () => {
  const galleryContainer = document.querySelector('.gallery-container');
  const filterBtns = document.querySelectorAll('.gallery-filter .btn');

  if (!galleryContainer || filterBtns.length === 0) return;

  filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      // Remove active class from all buttons
      filterBtns.forEach(btn => btn.classList.remove('active'));
      // Add active class to clicked button
      this.classList.add('active');

      const filterValue = this.getAttribute('data-filter');
      const items = galleryContainer.querySelectorAll('.gallery-item');

      items.forEach(item => {
        if (filterValue === 'all' || item.classList.contains(filterValue)) {
          item.style.display = 'block';
          // Optional: Add fade-in animation
          item.style.opacity = '0';
          setTimeout(() => {
            item.style.opacity = '1';
          }, 100);
        } else {
          item.style.display = 'none';
        }
      });
    });
  });
};

// Gallery Popup
const initGalleryPopup = () => {
  const galleryPopups = document.querySelectorAll('.gallery-popup');
  if (!galleryPopups.length) return;

  galleryPopups.forEach(popup => {
    popup.addEventListener('click', function(e) {
      e.preventDefault();
      const imageUrl = this.getAttribute('href');
      
      // Create modal
      const modal = document.createElement('div');
      modal.className = 'modal-preview';
      modal.innerHTML = `
        <span class="modal-close">&times;</span>
        <img src="${imageUrl}" alt="Preview">
      `;

      // Add to body
      document.body.appendChild(modal);
      document.body.style.overflow = 'hidden';

      // Close modal events
      modal.addEventListener('click', function() {
        this.remove();
        document.body.style.overflow = 'auto';
      });
    });
  });
};

// Initialize gallery when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
  try {
    initGallery();
    initGalleryPopup();
  } catch (error) {
    console.log('Gallery initialization error:', error);
  }
});

// Gallery Modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const closeBtn = document.querySelector('.modal-close');
    
    // Menambahkan event listener ke semua gallery popup links
    document.querySelectorAll('.gallery-popup').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = "flex";
            modalImg.src = this.getAttribute('href');
        });
    });

    // Menutup modal ketika tombol close diklik
    closeBtn.addEventListener('click', function() {
        modal.style.display = "none";
    });

    // Menutup modal ketika mengklik di luar gambar
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    // Menutup modal dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === "flex") {
            modal.style.display = "none";
        }
    });
});

// Counter Animation
const initCounters = () => {
  const counters = document.querySelectorAll('[data-counter]');
  
  const animateCounter = (counter) => {
    const target = parseInt(counter.getAttribute('data-counter'));
    let current = 0;
    const increment = target / 50; // Adjust speed here
    const duration = 2000; // 2 seconds
    const step = duration / 50;
    
    const updateCounter = () => {
      current += increment;
      if (current > target) {
        counter.textContent = target.toLocaleString() + '+';
      } else {
        counter.textContent = Math.floor(current).toLocaleString() + '+';
        requestAnimationFrame(updateCounter);
      }
    };
    
    updateCounter();
  };

  const observerCallback = (entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, {
    threshold: 0.5
  });

  counters.forEach(counter => observer.observe(counter));
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  try {
    initCounters();
  } catch (error) {
    console.log('Counter initialization error:', error);
  }
});
