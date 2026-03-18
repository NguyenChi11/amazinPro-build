(() => {
  const onReady = (fn) => {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn, { once: true });
      return;
    }
    fn();
  };

  const toInt = (value, fallback) => {
    const n = parseInt(String(value ?? ""), 10);
    return Number.isFinite(n) ? n : fallback;
  };

  const getCommentIdFromReplyLink = (link) => {
    if (!link) return "";
    return (
      link.getAttribute("data-commentid") ||
      (link.id && link.id.replace(/\D+/g, "")) ||
      ""
    );
  };

  const initChunkedStream = (streamEl, listEl) => {
    const chunk = Math.max(toInt(streamEl.getAttribute("data-chunk"), 20), 1);
    const order = String(
      streamEl.getAttribute("data-order") || "asc",
    ).toLowerCase();
    const isDesc = order === "desc";
    const topLevelComments = Array.from(listEl.children).filter((el) =>
      el.classList.contains("comment-item"),
    );

    if (!topLevelComments.length) return;

    let visibleStart = isDesc
      ? 0
      : Math.max(topLevelComments.length - chunk, 0);
    let visibleEnd = isDesc
      ? Math.min(chunk, topLevelComments.length)
      : topLevelComments.length;

    const applyVisibleWindow = () => {
      topLevelComments.forEach((item, index) => {
        const isVisible = index >= visibleStart && index < visibleEnd;
        item.style.display = isVisible ? "" : "none";
      });
    };

    applyVisibleWindow();

    // Start from newest edge depending on order.
    requestAnimationFrame(() => {
      streamEl.scrollTop = isDesc ? 0 : streamEl.scrollHeight;
    });

    streamEl.addEventListener(
      "scroll",
      () => {
        if (!isDesc) {
          // ASC: reveal older comments when scrolling to top.
          if (visibleStart <= 0) return;
          if (streamEl.scrollTop > 10) return;

          const prevHeight = streamEl.scrollHeight;
          const prevTop = streamEl.scrollTop;

          visibleStart = Math.max(visibleStart - chunk, 0);
          applyVisibleWindow();

          requestAnimationFrame(() => {
            const nextHeight = streamEl.scrollHeight;
            streamEl.scrollTop = nextHeight - prevHeight + prevTop;
          });
          return;
        }

        // DESC: reveal older comments when scrolling to bottom.
        if (visibleEnd >= topLevelComments.length) return;
        const distanceFromBottom =
          streamEl.scrollHeight - (streamEl.scrollTop + streamEl.clientHeight);
        if (distanceFromBottom > 10) return;

        const prevTop = streamEl.scrollTop;
        visibleEnd = Math.min(visibleEnd + chunk, topLevelComments.length);
        applyVisibleWindow();
        requestAnimationFrame(() => {
          streamEl.scrollTop = prevTop;
        });
      },
      { passive: true },
    );
  };

  const initReplyMove = (commentsRoot) => {
    const respond = document.getElementById("respond");
    const parentInput = document.getElementById("comment_parent");
    if (!respond || !parentInput) return;

    const ensureActionBar = () => {
      let bar = respond.querySelector(".bp-comment-respond-actions");
      if (!bar) {
        bar = document.createElement("div");
        bar.className = "bp-comment-respond-actions";
        const form = respond.querySelector("form");
        respond.insertBefore(bar, form || null);
      }

      let btn = bar.querySelector(".bp-comment-cancel-btn");
      if (!btn) {
        btn = document.createElement("button");
        btn.type = "button";
        btn.className = "bp-comment-cancel-btn";
        btn.textContent = "Cancel";
        bar.appendChild(btn);
      }

      return { bar, btn };
    };

    const { bar: actionBar, btn: cancelBtn } = ensureActionBar();
    actionBar.hidden = true;

    let placeholder = document.getElementById(
      "buildpro-comment-respond-placeholder",
    );
    if (!placeholder) {
      placeholder = document.createElement("div");
      placeholder.id = "buildpro-comment-respond-placeholder";
      respond.parentNode.insertBefore(placeholder, respond);
    }

    const clearReplyState = () => {
      commentsRoot
        .querySelectorAll(".comment-item.replying")
        .forEach((item) => item.classList.remove("replying"));
    };

    const restoreForm = () => {
      clearReplyState();
      parentInput.value = "0";
      actionBar.hidden = true;
      if (
        placeholder.parentNode &&
        respond.parentNode !== placeholder.parentNode
      ) {
        placeholder.parentNode.insertBefore(respond, placeholder.nextSibling);
      }
    };

    cancelBtn.addEventListener("click", restoreForm);

    const cancelLink = document.getElementById("cancel-comment-reply-link");
    if (cancelLink) {
      cancelLink.addEventListener("click", () => {
        setTimeout(restoreForm, 0);
      });
    }

    commentsRoot.addEventListener("click", (event) => {
      const link = event.target.closest(".comment-reply-link");
      if (!link) return;

      event.preventDefault();

      const commentId = getCommentIdFromReplyLink(link);
      const commentItem =
        (commentId && document.getElementById(`comment-${commentId}`)) ||
        link.closest(".comment-item");
      if (!commentItem) return;

      clearReplyState();
      commentItem.classList.add("replying");
      parentInput.value = commentId || "0";
      actionBar.hidden = false;

      const target = commentItem.querySelector(".reply") || commentItem;
      target.appendChild(respond);

      const textarea = respond.querySelector("textarea#comment");
      if (textarea) textarea.focus({ preventScroll: true });

      respond.scrollIntoView({ block: "nearest", behavior: "smooth" });
    });
  };

  onReady(() => {
    const commentsRoot = document.getElementById("comments");
    if (!commentsRoot) return;

    const commentsStream = document.getElementById("comments-stream");
    const commentsList = document.getElementById("comments-list");
    if (commentsStream && commentsList) {
      initChunkedStream(commentsStream, commentsList);
    }

    initReplyMove(commentsRoot);
  });
})();
