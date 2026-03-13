(() => {
  const onReady = (fn) => {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn, { once: true });
      return;
    }
    fn();
  };

  onReady(() => {
    const commentsRoot = document.getElementById("comments");
    const commentsStream = document.getElementById("comments-stream");
    const commentsList = document.getElementById("comments-list");
    const respond = document.getElementById("respond");
    const parentInput = document.getElementById("comment_parent");
    if (!commentsRoot || !respond || !parentInput) {
      return;
    }

    if (commentsStream && commentsList) {
      const chunk = Math.max(
        parseInt(commentsStream.getAttribute("data-chunk") || "20", 10) || 20,
        1,
      );
      const topLevelComments = Array.from(commentsList.children).filter((el) =>
        el.classList.contains("comment-item"),
      );

      let visibleStart = Math.max(topLevelComments.length - chunk, 0);

      const applyVisibleWindow = () => {
        topLevelComments.forEach((item, index) => {
          item.style.display = index >= visibleStart ? "" : "none";
        });
      };

      applyVisibleWindow();

      // Start from the latest comments (bottom), then load older ones when scrolling up.
      requestAnimationFrame(() => {
        commentsStream.scrollTop = commentsStream.scrollHeight;
      });

      commentsStream.addEventListener("scroll", () => {
        if (visibleStart <= 0) {
          return;
        }
        if (commentsStream.scrollTop > 10) {
          return;
        }

        const prevHeight = commentsStream.scrollHeight;
        const prevTop = commentsStream.scrollTop;

        visibleStart = Math.max(visibleStart - chunk, 0);
        applyVisibleWindow();

        requestAnimationFrame(() => {
          const nextHeight = commentsStream.scrollHeight;
          commentsStream.scrollTop = nextHeight - prevHeight + prevTop;
        });
      });
    }

    const placeholder = document.createElement("div");
    placeholder.id = "buildpro-comment-respond-placeholder";
    respond.parentNode.insertBefore(placeholder, respond);

    const clearReplyState = () => {
      commentsRoot
        .querySelectorAll(".comment-item.replying")
        .forEach((item) => item.classList.remove("replying"));
    };

    const restoreForm = () => {
      clearReplyState();
      parentInput.value = "0";
      if (
        placeholder.parentNode &&
        respond.parentNode !== placeholder.parentNode
      ) {
        placeholder.parentNode.insertBefore(respond, placeholder.nextSibling);
      }
    };

    const cancelLink = document.getElementById("cancel-comment-reply-link");
    if (cancelLink) {
      cancelLink.addEventListener("click", () => {
        setTimeout(restoreForm, 0);
      });
    }

    commentsRoot.addEventListener("click", (event) => {
      const link = event.target.closest(".comment-reply-link");
      if (!link) {
        return;
      }

      event.preventDefault();

      const commentId =
        link.getAttribute("data-commentid") ||
        (link.id && link.id.replace(/\D+/g, "")) ||
        "";
      const commentItem =
        (commentId && document.getElementById(`comment-${commentId}`)) ||
        link.closest(".comment-item");

      if (!commentItem) {
        return;
      }

      clearReplyState();
      commentItem.classList.add("replying");
      parentInput.value = commentId || "0";

      const target = commentItem.querySelector(".reply") || commentItem;
      target.appendChild(respond);

      const textarea = respond.querySelector("textarea#comment");
      if (textarea) {
        textarea.focus({ preventScroll: true });
      }

      respond.scrollIntoView({ block: "nearest", behavior: "smooth" });
    });
  });
})();
